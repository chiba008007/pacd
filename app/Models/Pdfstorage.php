<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdfstorage extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id','event_id','filenamecode', 'filename', 'type', 'create_date'];
}
