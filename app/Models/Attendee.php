<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendee extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'event_id',
        'event_number',
        'user_id',
        'event_join_id',
        'event_join_id_list',
        'paydate',
        'is_paid',
        'is_enabled_invoice',
        'discountSelectFlag',
        'discountSelectText',
        'tenjiSanka1Status',
        'tenjiSanka1Name',
        'tenjiSanka1Money',
        'tenjiSanka2Status',
        'tenjiSanka2Name',
        'tenjiSanka2Money',
        'konsinkaiSanka1Status',
        'konsinkaiSanka1Name',
        'konsinkaiSanka1Money',
        'konsinkaiSanka2Status',
        'konsinkaiSanka2Name',
        'konsinkaiSanka2Money'
    ];


    // カスタムフォーム項目の登録値取得
    public function custom_form_data()
    {
        return $this->hasMany(FormDataAttendee::class);
    }

    // ユーザー情報取得
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // イベント情報取得
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // 講演者情報取得
    public function presenters()
    {
        return $this->hasMany(Presenter::class);
    }

    // 参加費情報取得
    public function event_join()
    {
        return $this->belongsTo(Event_join::class);
    }

    // テストデータを返す
    static public function newTestData()
    {
        $test = new self([
            'event_id' => 0,
            'user_id' => 0,
            'event_join_id' => 0,
            'event_join_id_list' => 0,
            'is_paid' => 0,
            'is_paid' => 0,
            'discountSelectFlag' => 0,
            'discountSelectText' => 0,
        ]);
        $test->id = 0;
        return $test;
    }
}
