<?php

namespace App\Http\Controllers\Kyosan\Presenter;

use App\Http\Controllers\Controller;
use App\Library\PagesLibrary;
use App\Models\Attendee;
use App\Models\Presenter;
use App\Models\FormDataPresenter;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\User;
use App\Models\FormInput;
use App\Models\FormInputValue;
use App\Models\Presentation;

class RegisterController extends Controller
{
    private $form;
    private $attendee;
    private $user;
    private $event;

    public function __construct()
    {
        $this->middleware('auth');

        $this->form = config('pacd.form.type.touronkai_presenter');

        $current = Route::current();
        if ($current) {
            $this->attendee = Attendee::find($current->attendee_id);
            // 参加者情報が取得できない場合404エラー
            if (!$this->attendee) {
                abort(404);
            }
            // 例会の参加者でない場合エラー
            $this->event = $this->attendee->event;
            if ($this->event->category_type != config('pacd.category.touronkai.key')) {
                abort(404);
            }
        }
    }

    // 講演者登録ページ表示
    public function create($attendee_id)
    {
        $this->checkAttendee();

        $set = PagesLibrary::getContents(['route_name' => $this->form['prefix']],$this->event->id);
        $set['event'] = $this->event;
        $set['form'] = $this->form;
        $set['user'] = $this->user;
        $set['attendee'] = $this->attendee;

        return view('presenter.register', $set);
    }

    // 講演者登録処理
    public function store($attendee_id, Request $request)
    {

        $this->checkAttendee();

        if ($request->custom) {
            // カスタムインプット項目がある場合、バリデーション実行
            $request->validate(['custom.*' => new CustomFormDataRule()]);
        }

        // データ登録
        DB::beginTransaction();
        try {
            $presenter = Presenter::create(['attendee_id' => $this->attendee->id]);
            if ($request->custom) {
                FormDataPresenter::createFromInputData($request->custom, $presenter);
            }
            DB::commit();
            Log::info("【" . $this->form['display_name'] . "登録】presenter_id:$presenter->id");
            //presentation用空データ登録
            $presentationSet = [];
            $presentationSet[ 'presenter_id' ] = $presenter->id;
            $presentationSet[ 'description' ] = $request->description;
            $presentationSet[ 'daimoku' ] = $request->daimoku;
            $presentationSet[ 'enjya' ] = $request->enjya;
            $presentationSet[ 'syozoku' ] = $request->syozoku;
            $presentationSet[ 'gaiyo' ] = $request->gaiyo;
            Presentation::create($presentationSet);

            //メール配信
            $user = Auth::user();
            $this->mailsend($user->id,$presenter->id);

            // 管理者へメール通知
        //   Mail::to(config('admin.email'))->send(new \App\Mail\Admin\CreatePresenter($presenter, $this->form));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【" .  $this->form['display_name'] . "登録】attendee_id:" . $this->attendee->id . " error:" . $e->getMessage());

            return redirect()->back()->withInput()->with('status', '講演申し込みできませんでした。');
        }

        return redirect(route('touronkai_presenter.complete', $presenter->id));
    }




    // 講演登録可能な参加者か
    private function checkAttendee() {
        // ログイン中のユーザーの参加者情報でない場合エラー
        $this->user = Auth::user();
        if ($this->attendee->user_id != $this->user->id) {
            abort(404);
        }
    }

    //メール配信
    public function mailsend($userid,$presenter_id){

        //例会参加メール取得
        $form_type = config('pacd.CONST_MAIL_FORM_TEMP.touronkai.5.key');
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
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $body = preg_replace("/".$value['replace']."/",$userdata->$key,$body);
        }
        $forminput = FormInput::where(['form_type' => config('pacd.form.type.touronkai_presenter.key')])->get();

        foreach($forminput as $key=>$value){
            //登録フォームの値

            $forminputvalue = FormInputValue::where(['form_input_id'=>$value->id])->get();
            $write = "";
            foreach($forminputvalue as $k=>$val){
                $form_data_attendees = FormDataPresenter::where(['presenter_id'=>$presenter_id])
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
