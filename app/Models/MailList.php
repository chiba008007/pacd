<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailList extends Model
{
    protected $table = 'mails';
    protected $primaryKey = 'id';
    use HasFactory;


    /************************
     * イベント登録バリデーション
     */
    public function setValidate($request)
	{
        //バリデーションの実施
        $request->validate([
            'event_id' => ['required'],
            'sender_type' => ['required'],
            'subject' => ['required'],
            'body' => ['required'],
        ],
        [
            'event_id.required'   => '配信対象イベントを入力してください。',
            'sender_type.required'   => '配信対象者を入力してください。',
            'subject.required'   => 'タイトルを入力してください。',
            'body.required'   => '本文を入力してください。',
        ]);
    }


}
