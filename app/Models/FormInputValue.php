<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormInputValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'form_input_id',
        'value',
        'is_included_textarea',
    ];

    public function input()
    {
        return $this->belongsTo(FormInput::class);
    }
}
