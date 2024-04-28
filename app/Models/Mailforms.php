<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailforms extends Model
{
    use HasFactory;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'mailforms';
    protected $primaryKey = 'id';
    protected $fillable = [
        'form_type',
        'title',
        'note',
    ];
    public function __construct($category_type="")
    {

    }
    public static function editData($request){
        Mailforms::where("form_type",$request->form_type)
        ->update([
            'title'=>$request->title,
            'note'=>$request->note
        ]);
        return true;
    }

    public static function getData($form_type){
        $data = Mailforms::where("form_type",$form_type)->where("status",1)->first();
        return $data;
    }

}
