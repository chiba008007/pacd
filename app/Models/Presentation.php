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
