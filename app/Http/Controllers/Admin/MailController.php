<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\MailList;
use Illuminate\Support\Facades\Mail;
use App\Models\Upload;
use Illuminate\Support\Facades\Log;


class MailController extends Controller
{
    public function __construct()
    {
        $this->MailList = new MailList();
        $this->middleware('auth');
    }
    //
    public function index($category_prefix,$id=""){

        $set['title'] = config('pacd.category.'.$category_prefix.'.name').'一括メール送信';
        $category_type = config('pacd.category.'.$category_prefix.'.key');
        //配信対象イベント取得
        $event = Event::where('category_type',$category_type)->where("status",1)->orderBy('id','DESC')->get();
        $set['event'] = $event;
        $set['category_prefix'] = $category_prefix;
        //登録データ取得
        $set['data'] = MailList::where('status',1)->where('id',$id)->first();
        $set['id'] = $id;
        return view('admin.mailAll.index', $set);
    }
    //データ更新保存
    public function send(Request $request){

        $this->MailList->setValidate($request);
        //ファイルアップロード
        $filelink = $this->setUploadFile($request);
        if(isset($request->id) && $request->id){
            //idがあるときはupdate
            $this->MailList = MailList::find($request->id);
        }
        $this->MailList->subject = $request->subject;
        $body = $request->body;
        if($filelink){
            $body .= "\n\n\nファイルダウンロード\n";
            $body .= url('/')."/storage/".$filelink;
        }
        $this->MailList->body = $body;

        $this->MailList->event_id = $request->event_id;
        $this->MailList->sender_type = $request->sender_type;
        $this->MailList->sender_status = 0;
        if($this->MailList->save()){
            return redirect()->back()->with('status', '一括メール送信の更新を行いました');
        }else{
            return redirect()->back()->with('status', '一括メール送信の登録に失敗しました。');
        }
    }

    public static function setUploadFile($request){
        //ファイルがあればアップロード
        $file = $request->file('upload');
        if($file){
            $filename = "mails-".md5(uniqid());
            $ext = $file->getClientOriginalExtension();
            $file->storeAs('','public/'.$filename.'.'.$ext);
            return $filename.".".$ext;
        }
        return "";
    }

    //メール配信
    public function mailsend($category_prefix,$id){
        $mails = MailList::where('id',$id)->where('status',1)->first();

        $this->subject = $mails->subject;
        $this->body = $mails->body;
        //配信対象イベント取得
       $event = Event::where('id',$mails->event_id)->where("status",1)->first();
       $this->eventname = $event->name;

        if( $mails->sender_type == 3){
            // 企業協賛
            $sender = Attendee::join(
                'users','users.id','=','attendees.user_id'
            )
            ->where('attendees.event_id',$mails->event_id)->get();
            foreach($sender as $key=>$value){
                $this->mailsenddata($value);
            }
        }else
        if( $mails->sender_type == 1){
            //参加者取得
            $sender = Attendee::join(
                'users','users.id','=','attendees.user_id'
            )
            ->where('attendees.event_id',$mails->event_id)->get();
            foreach($sender as $key=>$value){
                $this->mailsenddata($value);
            }
        }else{
            //講演者
            $sender = Attendee::join(
                'users','users.id','=','attendees.user_id'
            )
            ->join('presenters', 'presenters.attendee_id', '=', 'attendees.id')
            ->where('attendees.event_id',$mails->event_id)
            ->groupBy('presenters.attendee_id')
            ->get();
            foreach($sender as $key=>$value){
                $this->mailsenddata($value);
            }
        }
        $this->MailListupdate = MailList::find($id);
        $this->MailListupdate->sender_status = 1;
        $this->MailListupdate->senddate = date('Y-m-d H:i:s');
        $this->MailListupdate->save();
        return redirect()->back()->with('status', '一括メール送信を行いました');
    }

    //メール配信
    public function mailsenddata($data){
        $body = $this->body;
        //本文置き換え
        $name = $data->sei." ".$data->mei;
        $body = preg_replace("/##ID##/",$data->login_id,$body);
        $body = preg_replace("/##NAME##/",$name,$body);
        $this->to = $data->email;
        $this->subject = $this->subject;

        mb_language("Japanese");
        mb_internal_encoding("UTF-8");


        $admin = config("admin.email");
        $head = config("admin.head");

        $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
       // $header.="\n";
        //$header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
        $pfrom = "-f$admin";


        if(config("app.env") === "local" ){
            //メール配信
            Mail::raw($body, function ($message) {
                $message->to($this->to)
                    ->subject($this->subject);
            });
        }else{
            // 本番
            mb_send_mail($this->to , $this->subject , $body,$header,$pfrom);

        }


        Log::info($this->eventname."【一括メール配信：" . $this->to.":名前:".$name."\n:本文:".$body);

        return true;

    }

    /******************
     * 一覧表示
     */
    public function list($category_prefix){

        $set['title'] = config('pacd.category.'.$category_prefix.'.name').'一括メール送信一覧';
        $category_type = config('pacd.category.'.$category_prefix.'.key');
        //配信メール取得
        $set['list'] = MailList::select([
            'events.name',
            'events.category_type',
            'mails.*'
            ])
            ->leftJoin('events','events.id','=','mails.event_id')
            ->where('mails.status','1')
            ->where('events.category_type',$category_type)
            ->orderBy('mails.id','desc')
            ->get();
        $set['category_prefix'] = $category_prefix;
        return view('admin.mailAll.list', $set);
    }
    /************
     * メール削除
     */
    public function delete($category_prefix,$id){
        $this->MailList = MailList::find($id);
        $this->MailList->status = 0;
        if($this->MailList->save()){
            return redirect()->back()->with('status', '一括メール送信の削除を行いました');
        }else{
            return redirect()->back()->with('status', '一括メール送信の削除に失敗しました。');
        }
    }
}
