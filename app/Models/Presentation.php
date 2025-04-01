<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'number',
        'presenter_id',
        'description',
        'daimoku',
        'enjya',
        'enjya1',
        'enjya2',
        'enjya3',
        'enjya4',
        'enjya5',
        'enjya6',
        'syozoku1',
        'syozoku2',
        'syozoku3',
        'syozoku4',
        'syozoku5',
        'syozoku6',
        'enjya_other',
        'syozoku',
        'gaiyo',
        'proceeding',
        'flash',
        'poster',
    ];

    // 講演者情報取得
    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
    }
}
