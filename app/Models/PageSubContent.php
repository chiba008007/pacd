<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSubContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_content_id',
        'content1',
        'content2',
        'column_count',
    ];
}
