<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request)
    {
        $set['token'] = $request->route()->parameter('token');
        $set['email'] = $request->email;
        return view('auth.passwords.reset')->with($set);
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|string',
            'login_id' => 'required|string',
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

        $user = User::Where('email', $request->email)->Where('login_id', $request->login_id)->first();

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }
}
