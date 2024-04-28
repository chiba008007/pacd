<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Yearpayment extends Model
{
    use HasFactory;


    protected $fillable = [
        'year',
        'bank_name',
        'bank_code',
        'invoice_address',
        'invoice_memo',
        'recipe_memo',

    ];


}
