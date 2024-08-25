<?php

namespace App\Http\Controllers\Kosyukai\Attendee;

use App\Http\Controllers\Controller;
use App\Library\PagesLibrary;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\FormDataAttendee;
use App\Models\Page;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\User;
use App\Models\FormInput;
use App\Models\FormInputValue;

class RegisterController extends Controller
{
    private $event;
    private $form;
    private $user;

    public function __construct()
    {
        $this->middleware('auth');

        $this->form = config('pacd.form.type.kosyukai_attendee');

        $current = Route::current();
        if ($current) {
            $this->event = Event::where('code', $current->event_code)->first();

            // 申し込みできないイベントの場合404エラー
            if (!$this->event || !$this->event->isEnabled() || $this->event->category_type != config('pacd.category.kosyukai.key')) {
                abort(404);
            }
        }
    }

    // 参加者登録ページ表示
    public function create($event_code)
    {
        // 無料会員は例会参加不可
        $this->user = Auth::user();
        //各イベントに参加申込できるクラス
        //法人会員・個人会員・会員外
        if (
            !($this->user->type == 3 ||
            $this->user->type == 2 ||
            $this->user->type == 4 ||
            $this->user->type == 1
            )
        ) {
            $joins = [
                config('pacd.user.type')[1],
                config('pacd.user.type')[3],
                config('pacd.user.type')[4]
            ];

            $page = Page::where(['route_name' => $this->form['prefix']])->first();
            $set['title'] = $page->title;
            $set['member'] = implode("/",$joins);
            return view('attendee.attention_authentication', $set);
        }


        // 既に申し込み済みの場合、参加者マイページにリダイレクト
      //  $this->user = Auth::user();
        $attendee = Attendee::where(['event_id' => $this->event->id, 'user_id' => $this->user->id])->first();
        if ($attendee) {
            return redirect(route('mypage.' . $this->form['category_prefix']));
        }

        $set = PagesLibrary::getContents(['route_name' => $this->form['prefix']],$this->event->id);
        $set['event'] = $this->event;
        $set['form'] = $this->form;
        $set['user'] = $this->user;

        return view('attendee.register', $set);
    }

    // 参加者登録処理
    public function store($event_code, Request $request)
    {

        // 既に申し込み済みの場合、参加者マイページにリダイレクト
        $this->user = Auth::user();

        $attendee = Attendee::where(['event_id' => $this->event->id, 'user_id' => $this->user->id])->first();
        if ($attendee) {
            return redirect(route('mypage.' . $this->form['category_prefix']));
        }

        $rules['event_join_id'] = 'nullable|exists:event_joins,id';
        if ($request->custom) {
            // カスタムインプット項目がある場合、バリデーション実行
            $rules['custom.*'] = new CustomFormDataRule();
        }
        if (!empty($request->event_join_id_list)) {
            $data['event_join_id_list'] = implode(",",$request->event_join_id_list);
        }
        $request->validate($rules);

        // データ登録
        DB::beginTransaction();
        try {
            $data['event_id'] = $this->event->id;
            $data['user_id'] = $this->user->id;
            if ($request->event_join_id) {
                $data['event_join_id'] = $request->event_join_id;
            }
            //event_numberの取得
            $data['event_number'] = Event::getEventNumber($this->event->id);
            //ダウンロード不可の初期状態を可にする
            $data['is_enabled_invoice'] = 1;
            if($request->discountSelectFlag){
                $data['discountSelectFlag'] = $request->discountSelectFlag;
                $data['discountSelectText'] = $request->discountSelectText;
            }
            $attendee = Attendee::create($data);
            $this->attendee = $attendee;
            if ($request->custom) {
                FormDataAttendee::createFromInputData($request->custom, $attendee);
            }
            DB::commit();
            Log::info("【" . $this->form['display_name'] . "登録】attendee_id:$attendee->id");

            $this->mailsend($this->user->id);

            // 管理者へメール通知
            //Mail::to(config('admin.email'))->send(new \App\Mail\Admin\CreateAttendee($attendee, $this->form));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【" .  $this->form['display_name'] . "登録】user_id:" . $this->user->id . ", error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('status', '参加申し込みできませんでした。');
        }

        $page = Page::where('route_name', $this->form['prefix'])->first();
        $set['title'] = $page->title . ' 受付完了';
        $set['edit_url'] = route('kosyukai_attendee.edit', [$attendee->id]);
        $set['create_url'] = route('kosyukai_presenter', [$attendee->id]);
        $set['userdata'] = $this->user;
        $set['prefix'] = $this->form['prefix'];
        $set['event'] = $this->event;
        return view('attendee.complete', $set);
    }

    //メール配信
    public function mailsend($userid){
        //例会参加メール取得
        $form_type = config('pacd.CONST_MAIL_FORM_TEMP.kosyukai.7.key');

        $mailformat = Mailforms::getData($form_type);

        //ユーザーデータ取得
        $userdata = User::where("id",$userid)->first();
        $this->to = $userdata->email;
        //タイトルの置き換え
        $title = $mailformat->title ?? '';
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $title = preg_replace("/".$value['replace']."/",$userdata->$key,$title);
        }
        $this->title = $title;
        //本文の置き換え
        $body = $mailformat->note;
        //イベント参加番号置き換え
        $event_number = sprintf("%010d",$this->attendee->event_number);
        $body = preg_replace("/##event_number##/",$event_number,$body);

        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $body = preg_replace("/".$value['replace']."/",$userdata->$key,$body);
        }

        $forminput = FormInput::where(['form_type' => config('pacd.form.type.kosyukai_attendee.key')])->get();
        $attendee = Attendee::where(['event_id' => $this->event->id, 'user_id' => $this->user->id])->first();


        foreach($forminput as $key=>$value){
            //登録フォームの値
            $forminputvalue = FormInputValue::where(['form_input_id'=>$value->id])->get();
            $write = "";
            foreach($forminputvalue as $k=>$val){

                $form_data_attendees = FormDataAttendee::where(['attendee_id'=>$attendee->id])
                ->where(["form_input_value_id"=>$val->id])
                ->first();
                if(!$form_data_attendees['data']) continue;
                $write .= $form_data_attendees['data'].$form_data_attendees[ 'data_sub' ]." ";
            }
            $body = preg_replace("/##mem".$value['id']."##/",$write,$body);

        }
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        $admin = config("admin.email");
        $head = config("admin.head");

        $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
        $header.="\n";
        $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
        $pfrom = "-f$admin";

        mb_send_mail($this->to , $this->title , $body,$header,$pfrom);
        Log::debug($this->event->name."【メール配信：" . $this->to."\n:本文:".$body);

        return true;
        /*
        //メール配信
        Mail::raw($body, function ($message) {
            $message->to($this->to)
                ->subject($this->title);
        });
        */
    }

}
