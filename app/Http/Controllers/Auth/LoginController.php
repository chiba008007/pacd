<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // ログイン後のリダイレクト先
    protected $redirectTo = RouteServiceProvider::MyPage;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // ログインに使用するカラムを`login_id`に設定
    public function username()
    {
        return 'login_id';
    }

    // 有効会員のみログイン
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['type'] = array_keys(config('pacd.user.type'));
        return $credentials;
    }
}
