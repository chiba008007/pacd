<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'type_number',
        'open_address_flag',
        'cp_name',
        'address',
        'busyo',
        'bumon',
        'tel',
        'fax',
        'groupname',
        'group_flag',
        'kyousan',
        'login_id',
        'password',
        'sei',
        'mei',
        'sei_kana',
        'mei_kana',
        'email',
        'postcode',
        'remarks',
        'type',
        'is_enabled_invoice',
    ];

    /**
     * パスワード再設定メールの送信
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token, $this->email));
    }

    // カスタムフォーム項目の登録値取得
    public function custom_form_data()
    {
        return $this->hasMany(FormDataCommon::class);
    }

    // カスタムフォーム項目の登録値取得
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    // テストデータを返す
    static public function newTestData()
    {
        $test = new self([
            'login_id' => 'sample_user',
            'sei' => 'テスト',
            'mei' => '太郎',
            'sei_kana' => 'てすと',
            'mei_kana' => 'たろう',
            'email' => 'test@sample.com',
        ]);
        $test->id = 0;
        return $test;
    }
}
