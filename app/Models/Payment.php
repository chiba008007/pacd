<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory;

    public static function setPayment($type = 1,$uid=""){
        //1月以降当年のデータが無ければ追加を行う
        $year = date("Y");
        $month = date("m");
        $user= Auth::user();
        if($uid) $user->id = $uid;
        $payment = Payment::where([
            'type'=>$type,
            'years'=>$year,
            'uid'=>$user->id,
            ])->first();
        if(!isset($payment->id) && $month >= 1){
            Payment::insert([
                'type'=>$type,
                'years'=>$year,
                'uid'=>$user->id,
                'status'=>0
            ]);
        }
    }
}
