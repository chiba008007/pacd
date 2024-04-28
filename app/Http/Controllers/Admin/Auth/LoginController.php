<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Auth;
use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    // 利用中のユーザー(status=1)のみログイン可
    public function username()
    {
        $username = request()->input('login_id');
        $field = 'login_id';
        request()->merge([$field => $username, 'status'=> 1]);
        return $field;
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password', 'status');
    }

    protected function guard()
    {
        return \Auth::guard('admin');
    }

    public function showLoginForm()
    {
        $set['title'] = '管理者ログイン';
        return view('admin.auth.login', $set);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect(route('admin.login'));
    }

    public function redirectPath()
    {
        return route('admin.home');
    }
}
