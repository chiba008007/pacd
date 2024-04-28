<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    //
    public function index(Request $request){

        $rules = $this->rules();
        $this->validate($request, $rules);
        //データ確認
        $user = User::where('login_id',$request->loginid)->where('email',$request->email)->first();
        if(empty($user->id)){
            return redirect()->back()->with('flash_message', '登録されているユーザは存在しません。');
        }else{
            $users = User::find($user->id);
            $users->password = Hash::make($request->password);;
            $users->save();
            return redirect()->back()->with('flash_message', 'パスワードの変更を行いました。');
        }

    }

    public function rules()
    {
        return [
            'loginid' => 'required',
            'email' => 'required',
            'password' => 'required|min:4|confirmed',
            'password_confirmation' => 'required',
        ];
    }
}
