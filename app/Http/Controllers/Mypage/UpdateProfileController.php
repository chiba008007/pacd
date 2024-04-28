<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FormInput;
use App\Models\FormInputValue;
use App\Models\FormDataCommon;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\Payment;

class UpdateProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 会員情報変更ページ表示
    public function edit()
    {
        $set['user'] = Auth::user();
        $set['inputs'] = FormInput::where(['form_type' => config('pacd.form.type.register.key'), 'is_display_published' => 1])->get();
        return view('mypage.profile.edit', $set);
    }

    // 会員情報更新処理（PUT/PATCH）
    public function update(UpdateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($user = User::find(Auth::id())) {
                $user->fill($request->all())->save();
                if ($request->custom) {
                    FormDataCommon::updateFormInputData($request->custom, $user);
                }
            }
            DB::commit();
            //管理者へメール配信
            $this->mailsend($user->id,$request);
            Log::info("【会員情報編集】user_id:$user->id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . '【会員情報編集】user_id:'. $user->id .', error:' . $e->getMessage());
            return redirect()->back()->with(['status' => '会員情報を変更できませんでした。']);
        }

        return redirect()->back()->with(['status' => '会員情報を変更しました。']);
    }

    public function mailsend($userid,$request=array()){
        //会員登録用メール取得
        $form_type = "";
        if(Auth::user()->type == 1) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.10.key');
        if(Auth::user()->type == 2) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.11.key');
        if(Auth::user()->type == 3) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.12.key');
        if(Auth::user()->type == 4) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.13.key');
        if(Auth::user()->type == 5) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.14.key');
        if(Auth::user()->type == 6) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.15.key');
        $mailformat = Mailforms::getData($form_type);

        //ユーザーデータ取得
        $userdata = User::where("id",$userid)->first();

        //$this->to = $userdata->email;
        $this->to = config('admin.email');
        $this->title = "会員情報変更のお知らせ[管理者用]";
        //本文の置き換え
        $body = "[このメールは管理者のみに送信されています]\n".$mailformat->note;
        //パスワードの変更
        $body = preg_replace("/##password##/", "[未変更]",$body);
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $body = preg_replace("/".$value['replace']."/",$userdata->$key,$body);
        }
        //フォームデータの取得
        $formdata = FormDataCommon::where("user_id",$userid)->get();
        foreach($formdata as $key=>$value){
            $body = preg_replace("/##mem".$value['form_input_value_id']."##/",$value->data,$body);
        }

        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        $admin = config("admin.email");
        $head = config("admin.head");

        $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
        $header.="\n";
    //    $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
    //    $header.="\n";
        $pfrom = "-f$admin";

        mb_send_mail($this->to , $this->title , $body,$header,$pfrom);

        return true;
/*
        //管理者へのみメール配信
        Mail::raw($body, function ($message) {
            $message->to($this->to)
                ->subject($this->title);
        });
*/

    }

    public function edit_password()
    {
        return view('mypage.profile.edit_password');
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'password' => 'required|alpha-num-check|min:4|max:16|confirmed',
        ]);

        $id = Auth::guard()->id();
        try {
            User::find($id)->update(['password' => Hash::make($request->password)]);
            Log::info("【会員パスワード変更】user_id:$id");
        } catch (\Exception $e) {
            Log::error(Route::currentRouteAction() . '【会員パスワード変更】user_id:'. $id .', error:' . $e->getMessage());
            return redirect()->back()->withInput()->with('status', 'パスワードを変更できませんでした。');
        }

        return redirect()->back()->with('status', 'パスワードを変更しました。');
    }
    public function payment(){
        //当年の支払い状況が登録されていなければ登録を行う
        Payment::setPayment();
        $user = Auth::user();
        $set['payment'] = Payment::where(['uid'=>$user->id])->orderBy("years","desc")->get();
        $set['user'] = $user;
        return view('mypage.profile.payment',$set);
    }
}
