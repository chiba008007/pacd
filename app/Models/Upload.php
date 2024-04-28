<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'uploads';
    protected $primaryKey = 'id';
    protected $fillable = [
        'event_id',
    ];

    public static function setUpload($file){
        $list = [];
        if(count($file)){

            foreach($file as $key=>$value){
                $ext = ($value->ext)??"";
                $filename = ($value->filename)??"";
                $list[$value->type]['dispname'] = ($value->dispname)??"";
                $list[$value->type]['filename'] = ($filename)?$filename.".".$ext:"";
                $list[$value->type]['lockkey'] = $value->lockkey;
            }
        }
        return $list;
    }
}
