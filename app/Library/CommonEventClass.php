<?php
namespace App\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Program;
use Illuminate\Support\Facades\Log;
use App\Models\Program_list;
use App\Models\Event;
use App\Models\Event_join;
use App\Models\Upload;

class CommonEventClass
{
    public function __construct($category_type)
    {
        $this->Event = new Event($category_type);
        $this->Program = new Program();
        $this->Programlist = new Program_list();
    }
    public static function sample(){
        echo "test";
        exit();
    }
    //イベントデータ追加
    public function setEvent($request){
        if($request->id){
            //更新処理
            DB::beginTransaction();
            try {
                $event = Event::find($request->id);
                $event->setRequest($event,$request);
                $event->save();
                $event_id = $request->id;
                foreach($request->join_status as $key=>$value){
                    //$event_join = new Event_join();
                    //更新対象データの取得
                    $data = Event_join::where('event_id',$event_id)
                    ->where('number',$key)
                    ->first();
                    $event_join = Event_join::find($data->id);
                    $event_join->setRequest($event_join,$key,$value,$request,$event_id);
                    $event_join->save();
                }

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
            }
        }else{
            //追加処理
            //リクエスト値をオブジェクトに保存
            DB::beginTransaction();
            try {
                $this->Event->setRequest($this->Event,$request);
                $this->Event->save();
                $event_id = $this->Event->id;
                foreach($request->join_status as $key=>$value){
                    $event_join = new Event_join();
                    $event_join->setRequest($event_join,$key,$value,$request,$event_id);
                    $event_join->save();
                }

                Log::info("【例会イベント登録】".$request->code);

                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
            }

        }
        return false;

    }
    //イベント選択時の日付データ取得
    public function getDateEvent($request){
        //日付データの取得
        $event = Event::find($request->event_id);
        $date_start=$event->date_start;
        $date_end=$event->date_end;
        $term = $this->Event->getTermDate($date_start,$date_end);
        $return['term'] = $term;
        //$return['webexUrl'] = $event->webex_url;
        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($return);
        exit();
    }

    //プログラム登録
    public function setProgram($request){
        //idの取得
        //eventがあるときはupdateを行う
        $event = Program::where('type',$request->type)
        ->where('event_id',$request->event_id)
        ->where('date',$request->date)
        ->first();
        if(!empty($event->id) ){
            DB::beginTransaction();
            try {
                $program = Program::find($event->id);
                $program->setRequest($program,$request);

                $program->save();
                Log::info("【例会プログラム更新】".$request->code);
                $this->setProgramList($request,$event->id);
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
            }
            return false;
        }else{
            DB::beginTransaction();
            try {
                //追加処理
                //リクエスト値をオブジェクトに保存
                $this->Program->setRequest($this->Program,$request);
                $this->Program->save();

                Log::info("【例会プログラム登録】".$request->code);
                $this->setProgramList($request,$this->Program->id);
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
            }
            return false;
        }
    }

    /**************
     * プログラム一覧登録
     */
    public function setProgramList(Request $request,$lastid){
        //イベント一覧保存
        CommonEventClass::commonSetProgramLists($request,$lastid);
    }


    //イベント一覧保存
    public static function commonSetProgramLists($request,$lastid){
        foreach($request->number as $key=>$value){
            $data = Program_list::where('program_id',$lastid)
            ->where('number',$key)
            ->first();

            if(!empty($data->id)){
                $Programlist = Program_list::find($data->id);
            }else{
                $Programlist = new Program_list();
            }
            $Programlist->event_id = $request->event_id;
            $Programlist->program_id = $lastid;
            $Programlist->number = $key;
            $Programlist->enable = $value;
            $Programlist->ampm = $request->ampm[$key];
            $Programlist->honor = $request->honor[$key];
            $Programlist->start_hour = $request->start_hour[$key];
            $Programlist->start_minute = $request->start_minute[$key];
            $Programlist->end_hour = $request->end_hour[$key];
            $Programlist->end_minute = $request->end_minute[$key];
            $Programlist->presentation_id = $request->presentation_id[$key];
            $Programlist->note = $request->note[$key];

            if(!empty($request->disp_status1[$key])){
                $Programlist->disp_status1 = 1;
            }else{
                $Programlist->disp_status1 = 0;
            }
            if(!empty($request->disp_status2[$key])){
                $Programlist->disp_status2 = 1;
            }else{
                $Programlist->disp_status2 = 0;
            }
            if(!empty($request->disp_status3[$key])){
                $Programlist->disp_status3 = 1;
            }else{
                $Programlist->disp_status3 = 0;
            }


            $Programlist->save();

        }

    }
    //イベント一覧取得
    public static function commonGetProgramLists($request){

        //日付データの取得
        $program = Program::where('type',$request->type)
        ->where('event_id',$request->event_id)
        ->where('date',$request->date)
        ->first();
        if(!empty($program->webex_url)){
            $return['webex_url'] = $program->webex_url;
        }
        if(!empty($program->explain)){
            $return['explain'] = $program->explain;
        }
        $programLists = [];
        if(!empty($program->id)){
         $programLists = Program_list::where('program_id',$program->id)->where("status",1)->get();
        }
        $list = [];
        if(count($programLists) > 0){
            foreach($programLists as $key=>$value){
                $list[$value->number]['number'      ] = $value->number;
                $list[$value->number]['enable'      ] = $value->enable;
                $list[$value->number]['ampm'        ] = $value->ampm;
                $list[$value->number]['start_hour'  ] = $value->start_hour;
                $list[$value->number]['start_minute'] = $value->start_minute;
                $list[$value->number]['end_hour'    ] = $value->end_hour;
                $list[$value->number]['end_minute'  ] = $value->end_minute;
                //講演者情報を取得する予定@
                $list[$value->number]['presentation_id' ] = $value->presentation_id;
                $list[$value->number]['note'     ] = $value->note;
            }
        }
        $return['list']=$list;
        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($return);
        exit();

    }
    //受付中ステータス切り替え
    public static function commonEditEnabled($request){
        $event = Event::find($request->id);
        if($request->chk === "true"){
            $event->enabled = 1;
        }else{
            $event->enabled = 0;
        }
        $event->save();
    }

    public static function setUploadFile($request,$type=1){
        //ファイルがあればアップロード
        $uploaddata = Upload::where('type',$type)
        ->where('event_id',$request->id)
        ->first();

        if(!empty($uploaddata->id )){
            $Upload = Upload::find($uploaddata->id);
        }else{
            $Upload = new Upload();
            $Upload->event_id = $request->id;
        }

        $fname = "upfile".$type."_name";
        $Upload->dispname = $request->$fname;

        $file= $request->file('upfile'.$type);
        if(!empty($file)){
            $filename = md5(uniqid());
            $Upload->filename = $filename;
            $ext = $file->getClientOriginalExtension();
            $Upload->ext = $ext;
            $file->storeAs('','public/'.$filename.'.'.$ext);
        }
        $Upload->type = $type;
        $lockkey="";
        if($type==1)$lockkey = $request->lockkey1;
        if($type==2)$lockkey = $request->lockkey2;
        if($type==3)$lockkey = $request->lockkey3;
        $Upload->lockkey = $lockkey;
        $Upload->save();


    }
    //CSVファイルの出力
    public static function createCsv($data,$head){

        // 書き込み用ファイルを開く
        $f = fopen('test.csv', 'w');
        if ($f) {
            // カラムの書き込み
            mb_convert_variables('SJIS', 'UTF-8', $head);
            fputcsv($f, $head);
            // データの書き込み
            foreach ($data as $user) {
            mb_convert_variables('SJIS', 'UTF-8', $user);
            fputcsv($f, $user);
            }
        }
        // ファイルを閉じる
        fclose($f);

        // HTTPヘッダ
        header("Content-Type: application/octet-stream");
        header('Content-Length: '.filesize('test.csv'));
        header('Content-Disposition: attachment; filename='.date('YmdHis').'.csv');
        readfile('test.csv');



    }

}

?>
