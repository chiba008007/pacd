<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'event_id',
        'type',
        'date',
        'explain',
    ];

    /******************
    * プログラム設定バリデーション
    */
    public function setValidateProgram($request){
        //バリデーションの実施
        $request->validate([
            'type' => ['required'],
            'event_id' => ['required'],
            'date' => ['required'],

        ],
        [
            'type.required'   => '会場を選択してください。',
            'event_id.required'   => 'イベントを選択してください。',
            'date.required'   => '日付を選択してください。',
        ]);
    }

    /***********
     * リクエストデータをオブジェクトにセット
     */
    public function setRequest($obj,$request){
        $obj->event_id=$request->event_id;
        $obj->type=$request->type;
        $obj->date=$request->date;
        $obj->explain=$request->explain;
        $obj->webex_url=$request->webex_url;
    }
    /******************************
     * プログラム一覧取得
     */
    public static function getProgram($id,$date){

        $program = Program::where(['event_id'=>$id])
            ->where(['date'=>$date])
            ->orderby('type')->get();
        $list = [];
        //typeをキーに変換
        if(count($program)){
            foreach($program as $key=>$value){
                $list[$value['type']] = $value;
            }
        }
        //プログラム一覧を取得
        $program_list = Program_list::where(['event_id'=>$id])
            ->where(['enable'=>1])
            ->orderby("number")
            ->get()
            ;
        //program_idをキーに変換
        $plist = [];
        if(count($program_list)){
            foreach($program_list as $key=>$value){
                $plist[$value['program_id']][] = $value;
            }
        }
        //一覧表示用に配列を併せる
        $return = [];
        foreach($list as $key=>$value){
            $return[$key] = $value;
            if(!empty($plist[ $value->id ])){
                $return[$key]['programlists'] = $plist[$value->id];
            }
        }
        return $return;
    }
    /******************************
     * プログラム一覧取得
    */
    public static function getProgramNow($id,$date){
        $h = date("H");
        $m = date("i");
        //プログラム一覧を取得
        $time = sprintf("%02d%02d",$h,$m);

        $sql = "event_id=".$id." AND CAST(CONCAT(lpad(start_hour,2,0),lpad(start_minute,2,0)) as signed) <= ".$time." AND CAST(CONCAT(lpad(end_hour,2,0),lpad(end_minute,2,0)) as signed) >=".$time;
        $program_list = Program_list::whereRaw($sql)->get();
        //program_idをキーに変換
        $plist = [];

        if(count($program_list)){
            foreach($program_list as $key=>$value){
                $plist[$value['program_id']] = $value;
                $plist[$value['program_id']]["start_hour"] = sprintf("%02d",$value->start_hour);
                $plist[$value['program_id']]["start_minute"] = sprintf("%02d",$value->start_minute);
                $plist[$value['program_id']]["end_hour"] = sprintf("%02d",$value->end_hour);
                $plist[$value['program_id']]["end_minute"] = sprintf("%02d",$value->end_minute);
            }
        }
        return $plist;
    }

}
