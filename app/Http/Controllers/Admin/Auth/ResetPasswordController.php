<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    public function showResetForm(Request $request)
    {
        $set['token'] = $request->route()->parameter('token');
        $set['title'] = 'パスワード再設定';
        $set['email'] = $request->email;
        return view('admin.auth.passwords.reset')->with($set);
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function broker()
    {
        return Password::broker('admins');
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email-check|max:255',
            'login_id' => 'required',
            'password' => 'required|confirmed|alpha-num-check|min:4|max:16',
        ];
    }

    public function credentials(Request $request)
    {
        return $request->only(
            'login_id', 'password', 'password_confirmation', 'token', 'email'
        );
    }

    public function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)]
            ]);
        }

        return redirect()->back()
                    ->withInput($request->only('email', 'login_id'))
                    ->withErrors(['login_id' => trans($response)]);
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $user = Admin::Where('email', $request->email)->Where('login_id', $request->login_id)->first();

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }

    public function redirectPath()
    {
        return route('admin.home');
    }
}
