<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Program;

use Illuminate\Support\Facades\Log;
use App\Models\Program_list;
use App\Models\Event_join;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Library\CommonEventClass;
use App\Models\Presentation;
use App\Models\Upload;
use App\Models\eventPassword;

class ReikaiController extends Controller
{
    public $page = 5;
    public $category_type = 0;
    public $listLoop = 10;
    public $maxdate = "2999-01-01";
    public function __construct()
    {
        //カテゴリタイプの指定
        $this->category_prefix = config('pacd.category.reikai.prefix');
        $this->category_type = config('pacd.category.reikai.key');
        $this->category_title = config('pacd.category.reikai.name');
        $this->Event = new Event($this->category_type);
        $this->EventJoin = new Event_join();
        $this->Program = new Program();
        $this->Programlist = new Program_list();
        $this->Common = new CommonEventClass($this->category_type);

    }

    public $eventtype = "reikai";
    public function passwordcheck(){
        $currentPath= Route::getFacadeRoot()->current()->uri();
        $set['currentPath'] = $currentPath;
        $set['eventtypepath'] = $this->eventtype;
        return view('admin.members.eventtypeForm', $set);
    }
    public function passwordchecked(Request $request){
        $data = eventPassword::where("eventtype","=",$this->eventtype)
        ->where("password","=",$request->password)
        ->count();
        if($data > 0){
            // sessionに登録
            $request->session()->put($this->eventtype, true);
            return redirect(route('admin.reikai.event.list'));
        }
        return redirect()->back()->withInput()->with('flash.error', '認証に失敗しました。');
    }
    public function checked(){
        $data = session($this->eventtype);
        if(!$data){
            $url = url('/') . "/" . config("admin.uri")."/".$this->eventtype."/password/check";
            return redirect()->to($url)->send();
        } 
    }

    //イベント一覧
    public function event_list(Request $request){
        $this->checked();

        $events = Event::where('status',1)
        ->where('category_type',$this->category_type);
        $this->request = $request;
        $events=$events->where(function($events){
            if($this->request->code) $events = $events->orwhere('name','like','%'.$this->request->code.'%');
            if($this->request->code) $events = $events->orwhere('code','like','%'.$this->request->code.'%');
        });
        $events = $events
            ->orderby("date_start","desc")
            ->paginate($this->page);
        $set['title'] = $this->category_title.'イベント一覧';
        $set['pages'] = [];
        $set['code'] = $request->code;
        $set['events'] = $events;
        $set['category_prefix'] = $this->category_prefix;
        return view('admin.event.list', $set);
    }
    /******************
     * イベント登録フォーム
     */
    public function event_regist($id=0){
        $set['title'] = $this->category_title.'イベント登録';
        $set['listLoop'] = $this->listLoop;
        //イベントデータの取得
        $event=[];
        if($id) $event = Event::find($id);
        $param = $this->Event->dispParam($event);
        $set = array_merge($set,$param);
        $eventjoin=[];
        if($id){
            $eventjoin = $this->EventJoin->where("event_id",$id)->where("status","1")->get();
        }
        $paramJoin = $this->EventJoin->dispParam($eventjoin,$this->listLoop);
        $set = array_merge($set,$paramJoin);
        $set['category_prefix'] = $this->category_prefix;

        $set['date_start'] = Event::replace2999($set[ 'date_start' ]);
        $set['date_end'] = Event::replace2999($set[ 'date_end' ]);
        return view('admin.event.regist', $set);
    }
    /**************
     * イベント登録/更新
     */
    public function event_add(Request $request){

        //バリデートの実施
        $this->Event->setValidate($request);
        //時刻の設定
        $request->date_start_time = sprintf("%02d:%02d"
            ,$request->date_start_time1
            ,$request->date_start_time2
        );
        $request->date_end_time = sprintf("%02d:%02d"
            ,$request->date_end_time1
            ,$request->date_end_time2
        );
        if(!$request->date_start) $request->date_start = $this->maxdate;
        if(!$request->date_end) $request->date_end = $this->maxdate;

        if($this->Common->setEvent($request)){
            return redirect()->back()->with('status', 'イベント情報の更新を行いました');
        }else{
            return redirect()->back()->with('status', 'イベント情報の登録に失敗しました。');
        }
    }
    /****************
     * イベント削除
     */
    public function event_delete($id){
        if($id > 0){
            $event = Event::find($id);
            $event->status = 0;
            if ($event->save()) {
                Log::info("【例会イベント削除】code=>".$event->code);
                return redirect()->back()->with('status', 'イベント情報の削除を行いました');
            }
        }
    }
    /*******************
     * プログラム設定
     */
    public function event_program(request $request,$id=0){
        //イベントの取得
        $event = Event::where('status',1)->where("category_type",$this->category_type)->orderBy('id')->get();

        //プログラム情報の取得
        $filecount = 0;
        $program = [];
        if(
            $request->get( 'event_id' )
            && $request->get( 'date' )
            && $request->get( 'type' )
        ){
            $sql = "
                SELECT
                    p.*,
                    pl.*,
                    pre.number,
                    pre.proceeding,
                    pre.flash,
                    pre.poster
                FROM
                    programs as p
                    LEFT JOIN program_lists as pl ON p.event_id = pl.event_id AND pl.program_id = p.id
                    LEFT JOIN presentations as pre ON pre.id = pl.presentation_id
                WHERE
                    p.event_id=:event_id AND
                    p.date=:date AND
                    p.type =:type
                ORDER BY pl.number ASC
            ";
            $program = DB::select($sql,
            [
                'event_id' => $request->get('event_id'),
                'date' => $request->get('date'),
                'type' => $request->get('type')
            ]);


            //ファイルの総数を確認
            foreach($program as $key=>$value){
                if(
                    $value->proceeding
                    || $value->flash
                    || $value->poster
                ){
                    $filecount += 1;
                }
            }

        }
        $set = [];
        $set[ 'id'  ] = $id;
        $set['title'] = $this->category_title.'プログラム設定';
        $set['event'] = $event;
        $set['category_prefix'] = $this->category_prefix;
        $set['request'] = $request;
        $set['filecount'] = $filecount;
        if(!empty($program)){
            $set['program'] = $program[0];
        }else{
            $set[ 'program' ] = [];
        }
        $set['programlist'] = $program;
        return view('admin.event.program', $set);
    }
    /**********************
     * イベント選択時の日付データ取得
     */
    public function dateAjax(Request $request){
        $this->Common->getDateEvent($request);
    }
    /***************
     * プログラム設定登録
     */
    public function event_program_add(Request $request){
        //バリデートの実施
        $this->Program->setValidateProgram($request);
        if($this->Common->setProgram($request)){
            return redirect()->back()->with('status', 'プログラム情報の更新を行いました');
        }else{
            return redirect()->back()->with('status', 'プログラム情報の更新に失敗しました');
        }
    }
    /**********************
    * イベント選択時の登録データ取得
    */
    public function getAjax(Request $request){
        //イベント一覧保存
        CommonEventClass::commonGetProgramLists($request);
    }
    /****************
     * 参加受付の変更
     */
    public function enableAjax(Request $request){
        CommonEventClass::commonEditEnabled($request);
        exit();
    }

    /*******************
     * ファイルアップロード
     */
    public function event_upload($id=0){
        //ファイル情報取得
        $file = Upload::where('status',1)->where('event_id',$id)->get();
        $list = Upload::setUpload($file);
        $set = [];
        $set[ 'id'  ] = $id;
        $set['title'] = $this->category_title.'ファイルアップロード';
        $set['category_prefix'] = $this->category_prefix;
        $set['lists'] = $list;
        return view('admin.event.event_upload', $set);
    }
    /*******************
     * ファイルアップロード
     */
    public function event_upload_add(Request $request){
        //ファイル名
        CommonEventClass::setUploadFile($request,1);
        CommonEventClass::setUploadFile($request,2);
        CommonEventClass::setUploadFile($request,3);
        return redirect()->back()->with('status', 'ファイルのアップロードを行いました');
    }


}
