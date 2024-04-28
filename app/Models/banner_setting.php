<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class banner_setting extends Model
{
    use HasFactory;
    protected $table = 'banner_setting';
    protected $primaryKey = 'id';
    protected $fillable = [
        'smooth'
    ];
}
