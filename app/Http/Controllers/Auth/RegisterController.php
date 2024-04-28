<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FormDataCommon;
use App\Models\FormInputValue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;

// ユーザー新規登録コントローラー
// Illuminate\Foundation\Auth\RegistersUsers（Laravel標準新規登録）未使用
class RegisterController extends Controller
{
    // 新規登録後のリダイレクト先
    private $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(CreateUserRequest $request)
    {

        DB::beginTransaction();
        try {
            $request->merge(['password' => Hash::make($request->password)]);
            $user = User::create($request->all());
            if ($request->custom) {
                FormDataCommon::createFromInputData($request->custom, $user);
            }
            DB::commit();
            $this->mailsend($user->id,$request);


            Log::info("【会員登録】user_id:$user->id");
            // 管理者へメール通知
            //Mail::to(config('admin.email'))->send(new \App\Mail\Admin\CreateUser($user, config('pacd.form.type.register')));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . '【会員登録】error:' . $e->getMessage());
            return redirect()->back()->withInput()->with(['status' => '会員登録できませんでした。']);
        }

        Auth::guard()->login($user);
        return redirect($this->redirectTo);

    }

    //メール配信
    public function mailsend($userid,$request=array()){
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        //ユーザーデータ取得
        $userdata = User::where("id",$userid)->first();
        $this->to = $userdata->email;


        //会員登録用メール取得
        //会員区分事に分ける
        $type = $userdata->type;
        $form_type = "";

        if($type == 1) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.10.key');
        if($type == 3) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.12.key');
        if($type == 5) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.14.key');
        if($type == 6) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.15.key');
        $mailformat = Mailforms::getData($form_type);

        //タイトルの置き換え
        $title = $mailformat->title ?? '';
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $title = preg_replace("/".$value['replace']."/",$userdata->$key,$title);
        }
        $this->title = $title;
        //本文の置き換え
        $body = $mailformat->note;
        //パスワードの変更
        $body = preg_replace("/##password##/", $_REQUEST['password'],$body);
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $body = preg_replace("/".$value['replace']."/",$userdata->$key,$body);
        }
        //フォームデータの取得
        $formdata = FormDataCommon::where("user_id",$userid)->get();
        foreach($formdata as $key=>$value){
            $body = preg_replace("/##mem".$value['form_input_value_id']."##/",$value->data,$body);
        }

        $admin = config("admin.email");
        $head = config("admin.head");

        $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
        $header.="\n";
        $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
        $pfrom = "-f$admin";

        mb_send_mail($this->to , $this->title , $body,$header,$pfrom);

/*
        //メール配信
        Mail::raw($body, function ($message) {
            $message->to($this->to)
                ->subject($this->title);
        });
*/
return true;

    }
}
