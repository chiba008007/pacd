<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\eventPassword;

class UpdateProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function showUpdateEmailForm()
    {
        $set['title'] = 'メールアドレス変更';
        return view('admin.auth.update_email', $set);
    }

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email-check']);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $admin = Admin::find(Auth::guard('admin')->id());
        $admin->email = $request->input('email');
        $admin->save();
        return redirect()->back()->with('status', 'メールアドレスを変更しました。');
    }

    public function showUpdatePasswordForm()
    {
        $set['title'] = 'パスワード変更';
        return view('admin.auth.passwords.update', $set);
    }
    public function showEventUpdatePasswordForm()
    {
        if(Auth::guard('admin')->user()->login_id != "main"){
            echo "error";
            exit();
        }

        $items = eventPassword::get();
        $data = [];
        $label = [];
        $eventtypeTitle[ 'members' ] = "会員管理";
        $eventtypeTitle[ 'reikai' ] = "例会&講演会";
        $eventtypeTitle[ 'touronkai' ] = "高分子分析討論会";
        $eventtypeTitle[ 'kyosan' ] = "企業協賛";
        $eventtypeTitle[ 'kosyukai' ] = "高分子分析技術講習会";
        $eventtypeTitle[ 'pages' ] = "公開ページ管理";
        foreach ($items as $item) {
            $data[$item->eventtype] = $item->eventtype;
            $password[$item->eventtype] = $item->password;
            $label[$item->id] = $item->eventtype;
        }
        $set['title'] = '各イベントパスワード変更';
        $set['data'] = $data;
        $set['label'] = $label;
        $set['eventtypeTitle'] = $eventtypeTitle;
        $set['password'] = $password;
        return view('admin.auth.passwords.eventUpdate', $set);
    }
    public function postEventUpdatePasswordForm(Request $request)
    {
        // データ取得
        foreach($request->eventtype as $key=>$value){
            eventPassword::find($key)->update(['password'=>$value]);
        }
        return redirect()->back()->with('status', 'パスワードを変更しました。');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'alpha-num-check', 'min:4', 'max:16', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        Admin::find(Auth::guard('admin')->id())->update(['password' => Hash::make($request->password)]);
        return redirect()->back()->with('status', 'パスワードを変更しました。');
    }
}
