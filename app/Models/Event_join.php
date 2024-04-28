<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event_join extends Model
{
    use HasFactory;
     /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'event_joins';
    protected $primaryKey = 'id';

    public function setRequest($obj,$key,$value,$request,$last_id){

        $obj->event_id = $last_id;
        $obj->number = $key;
        $obj->join_status = $value;
        $obj->join_name = $request->join_name[$key];
        $obj->join_price = (int)$request->join_price[$key];
        $obj->join_fee = (int)$request->join_fee[$key];
        $obj->pattern = (int)$request->pattern[$key];

    }

    /**************
     * 表示イベント変数
     */
    public function dispParam($param,$loop){
        //DBの値

        $set = [];
        for($i=0;$i<$loop;$i++){
            $set['join_status'  ][$i] = ($param[$i]->join_status)??"";
            $set['join_name'    ][$i] = ($param[$i]->join_name)??"";
            $set['join_price'   ][$i] = ($param[$i]->join_price)??"";
            $set['join_fee'     ][$i] = ($param[$i]->join_fee)??"";
            $set['pattern'     ][$i] = ($param[$i]->pattern)??"";
        }
        return $set;
    }
    /************
     * データ取得
     */
    public static function getEventJoin(){
        $data = Event_join::orderBy('number')->where('status',1)->where('join_status',1)->get();
        $list = [];
        foreach($data as $key=>$value){
            $list[$value->event_id][] = $value;
        }
        return $list;
    }
}
