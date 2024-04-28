<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
