<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program_list extends Model
{
    use HasFactory;
    protected $table = 'program_lists';
    protected $primaryKey = 'id';
    protected $fillable = [
        'event_id',
        'program_id',
        'number',
        'enable',
        'ampm',
        'start_hour',
        'start_minute',
        'end_hour',
        'end_minute',
        'presentation_id',
        'note',
    ];

    // 講演内容取得
    public function presentation()
    {
        return $this->belongsTo(Presentation::class);
    }

    /***********
     * リクエストデータをオブジェクトにセット
     */
    public function setRequest($obj,$request){
        $obj->event_id=$request->event_id;
        $obj->program_id=$request->program_id;
        $obj->number=$request->number;
        $obj->enable=$request->enable;
        $obj->ampm=$request->ampm;
        $obj->start_hour=$request->start_hour;
        $obj->start_minute=$request->start_minute;
        $obj->end_hour=$request->end_hour;
        $obj->end_minute=$request->end_minute;
        $obj->presentation_id=$request->presentation_id;
        $obj->note=$request->note;
        $obj->disp_status1=$request->disp_status1;
        $obj->disp_status2=$request->disp_status2;
        $obj->disp_status3=$request->disp_status3;


    }

}
