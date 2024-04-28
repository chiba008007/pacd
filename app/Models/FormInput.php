<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormInput extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'event_id',
        'form_type',
        'name',
        'type',
        'validation_rules',
        'validation_message',
        'is_display_published',
        'is_display_user_list',
        'csvflag',
        'csvtag',
    ];

    public function values()
    {
        return $this->hasMany(FormInputValue::class);
    }

    // validation_rolesの表示用カラム追加
    public function getValidationRulesDisplayAttribute()
    {
        $disps = [];
        if (strpos($this->validation_rules, 'required') !== false) {
            $disps[] = '必須';
        }
        if (strpos($this->validation_rules, 'numeric') !== false) {
            $disps[] = '数値';
        }
        if (strpos($this->validation_rules, 'alpha-check') !== false) {
            $disps[] = '半角英字';
        }
        if (strpos($this->validation_rules, 'alpha-num-check') !== false) {
            $disps[] = '数値';
            $disps[] = '半角英字';
        }

        return $disps;
    }
}
