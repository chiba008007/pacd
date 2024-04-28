<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'attendee_id',
    ];


    // カスタムフォーム項目の登録値取得
    public function custom_form_data()
    {
        return $this->hasMany(FormDataPresenter::class);
    }

    // 参加者情報取得
    public function attendee()
    {
        return $this->belongsTo(Attendee::class);
    }

    // 講演情報取得
    public function presentation()
    {
        return $this->hasOne(Presentation::class);
    }
}
