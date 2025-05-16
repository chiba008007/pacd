<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kyosanTitle extends Model
{
    use HasFactory;
    protected $table = 'kyosantitles';
    protected $fillable = [
        'tenjikaiTitle',
        'tenjikaiNote',
        'konsinkaiTitle',
        'konsinkaiNote',
    ];
}
