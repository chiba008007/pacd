<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class eventPassword extends Model
{
    use HasFactory;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'event_passwords';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'eventtype',
        'password',
    ];
    public function __construct($category_type = "")
    {
        $this->category_type = $category_type;
    }
}
