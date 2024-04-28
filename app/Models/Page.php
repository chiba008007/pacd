<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\PageContent;
use App\Models\PageSubContent;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'uri',
        'route_name',
        'is_form',
        'is_opened',
    ];
}
