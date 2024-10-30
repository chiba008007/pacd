<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendee;

class Event extends Model
{
    use HasFactory;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'code',
        'date_start',
        'date_end',
        'place',
        'bank_name',
        'bank_code',
        'invoice_address',
        'invoice_memo',
        'recipe_memo',
    ];
    public function __construct($category_type = "")
    {
        $this->category_type = $category_type;
    }

    //日付の置き換え
    //2999年から始まるものは空欄で返す
    static function replace2999($str)
    {
        if (preg_match("/^2999/", $str)) {
            $str = "";
        }
        return $str;
    }
    // 参加者情報取得
    public function event_joins()
    {
        return $this->hasMany(Event_join::class);
    }

    // 参加者情報取得
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    // 申し込み可能イベントかどうか
    public function isEnabled()
    {
        return $this->enabled && $this->status;
    }

    // テストデータを返す
    static public function newTestData()
    {
        $test = new self(1);
        $test->fill([
            'name' => 'プレビュー用テストイベントデータ',
            'date_start' => '2021-02-22',
            'date_end' => '2021-02-22',
            'place' => 'テスト会場',
            'code' => 'event_sample_code',
        ]);
        $test->id = 0;
        return $test;
    }

    /************************
     * イベント登録バリデーション
     */
    public function setValidate($request)
    {
        //バリデーションの実施
        $request->validate(
            [
                'code' => ['required', 'unique:events,code,' . $request->id . ',id', 'hankaku-check'], 'name' => ['required']

                /*
            ,'date_start' => ['required',function($attribute, $value, $fail){
                $this->date_start = $value;
            }]
            ,'date_end' => ['required',function($attribute, $value, $fail){
                if($this->date_start > $value){
                    $fail('終了日時は開始日時より後にしてください。');
                }
            }]
            */
            ],
            [
                'code.required'   => 'イベントコードを入力してください。',
                'code.unique'   => 'イベントコードが重複しています。',
                'name.required'   => 'イベント名を入力してください。',
                'date_start.required'   => '開催日を入力してください。',
                'date_end.required'   => '終了日を入力してください。',
            ]
        );
    }


    /***********
     * リクエストデータをオブジェクトにセット
     */
    public function setRequest($obj, $request)
    {
        $obj->code = $request->code;
        $obj->sponser = $request->sponser;
        $obj->name = $request->name;
        $obj->discountFlag = $request->discountFlag;
        $obj->discountRate = sprintf("%d",$request->discountRate);
        $obj->discountText = ($request->discountText)?$request->discountText:"";
        $obj->event_info = $request->event_info;
        $obj->date_start = $request->date_start;
        $obj->date_start_time = $request->date_start_time1 . ":" . $request->date_start_time2;
        $obj->date_end = $request->date_end;
        $obj->date_end_time = $request->date_end_time1 . ":" . $request->date_end_time2;
        $obj->place = $request->place;
        $obj->event_address = $request->event_address;
        $obj->party = $request->party;
        $obj->party_address = $request->party_address;
        $obj->bank_name = $request->bank_name;
        $obj->bank_code = $request->bank_code;
        $obj->sanka_explain = $request->sanka_explain;
        $obj->map_status = $request->map_status;
        // $obj->webex_url=$request->webex_url;
        $obj->other = $request->other;
        $obj->category_type = $this->category_type;
        $obj->join_enable = $request->join_enable;
        $obj->presenter_flag = $request->presenter_flag;
        $obj->coworker = $request->coworker;
        $obj->event_type = $request->event_type;
        $obj->event_type = $request->event_type;
        $obj->invoice_address = $request->invoice_address;
        $obj->invoice_memo = $request->invoice_memo;
        $obj->recipe_memo = $request->recipe_memo;
        $obj->outputtype = $request->outputtype;
    }
    /**************
     * 表示イベント変数
     */
    public function dispParam($param)
    {
        //DBの値
        $date_start_time1 = "";
        $date_start_time2 = "";
        $date_end_time1 = "";
        $date_end_time2 = "";

        if (!empty($param->date_start_time)) {
            $ex  = explode(":", $param->date_start_time);
            $date_start_time1 = $ex[0];
            $date_start_time2 = $ex[1];
        }
        if (!empty($param->date_end_time)) {
            $ex  = explode(":", $param->date_end_time);
            $date_end_time1 = $ex[0];
            $date_end_time2 = $ex[1];
        }
        $set['id'] = ($param->id) ?? "";
        $set['code'] = ($param->code) ?? "";
        $set['sponser'] = ($param->sponser) ?? "";
        $set['name'] = ($param->name) ?? "";
        $set['discountFlag'] = ($param->discountFlag) ?? "";
        $set['discountText'] = ($param->discountText) ?? "";
        $set['discountRate'] = ($param->discountRate) ?? "";
        $set['event_info'] = ($param->event_info) ?? "";
        $set['date_start'] = ($param->date_start) ?? "";
        $set['date_end'] = ($param->date_end) ?? "";
        $set['date_start_time1'] = ($date_start_time1) ?? "";
        $set['date_end_time1'] = ($date_end_time1) ?? "";
        $set['date_start_time2'] = ($date_start_time2) ?? "";
        $set['date_end_time2'] = ($date_end_time2) ?? "";
        $set['place'] = ($param->place) ?? "";
        $set['event_address'] = ($param->event_address) ?? "";
        $set['party'] = ($param->party) ?? "";
        $set['party_address'] = ($param->party_address) ?? "";
        $set['sanka_explain'] = ($param->sanka_explain) ?? "";
        $set['bank_name'] = ($param->bank_name) ?? config('pacd.bank.name');
        $set['bank_code'] = ($param->bank_code) ?? config('pacd.bank.code');
        $set['map_status'] = ($param->map_status) ?? "0";
        $set['other'] = ($param->other) ?? "";
        $set['outputtype'] = $param->outputtype ?? 1;
        $set['join_enable'] = ($param->join_enable) ?? "0";
        $set['presenter_flag'] = ($param->presenter_flag) ?? "0";
        $set['coworker'] = ($param->coworker) ?? "";
        $set['event_type'] = ($param->event_type) ?? "1";
        $set['invoice_address'] = ($param->invoice_address) ?? config('pacd.bank.invoice_address');
        $set['invoice_memo'] = ($param->invoice_memo) ?? config('pacd.bank.invoice_memo');
        $set['recipe_memo'] = ($param->recipe_memo) ?? config('pacd.bank.recipe_memo');


        return $set;
    }
    /*****************
     * 日付の範囲取得
     */
    public function getTermDate($startDate, $endDate)
    {
        $diff = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        for ($i = 0; $i <= $diff; $i++) {
            $period[] = date('Y-m-d', strtotime($startDate . '+' . $i . 'days'));
        }
        return $period;
    }
    /******************
     * イベント次回のイベント情報取得
     */
    public static function getEventLists()
    {
        //通常イベント
        $events = self::getEventListsData(1);
        //各イベントごとに配列作成
        $list['events'] = [];
        $list['coworks'] = [];
        foreach ($events as $key => $value) {
            $list['events'][$value->category_type][] = $value;
        }
        //協賛イベント
        $cowork = self::getEventListsData(2);
        foreach ($cowork as $key => $value) {
            if ($value->enabled) {
                $list['coworks'][$value->category_type][] = $value;
            }
        }
        return $list;
    }

    public static function getEventListsData($event_type = 1)
    {
        $now = date("Y-m-d");
        //次回のイベント一覧
        $events = Event::where('status', 1)->where('event_type', $event_type)->where('date_end', '>=', $now)->orderBy('category_type', 'ASC')->orderBy('date_start', 'ASC')->get();
        return $events;
    }
    /******************
     * イベント次回のイベント情報取得カテゴリ別
     */
    public static function getEventListsCategory($category_type)
    {
        //次回のイベント一覧
        $now = date("Y-m-d");
        $events = Event::select([
            'events.id', 'events.sponser', 'events.name', 'events.event_info', 'events.category_type', 'events.code', 'events.event_type', 'events.coworker', 'events.date_start', 'events.date_end', 'events.place', 'events.event_address', 'events.party', 'events.party_address', 'events.bank_name', 'events.bank_code', 'events.sanka_explain', 'events.other', 'uploads.filename', 'uploads.dispname', 'uploads.ext', 'uploads.id as upload_id'
        ])
            ->where('events.status', 1)
            ->leftJoin("uploads", 'uploads.event_id', 'events.id')
            ->where('events.category_type', $category_type)
            ->where('events.date_start', '<=', $now)
            ->orderBy('events.date_start', 'desc')
            ->get();
        //データ配列の分割
        $lists = [];
        if (count($events)) {
            $i = 0;
            foreach ($events as $key => $value) {
                $lists[$value->id]['id'] = $value->id;
                $lists[$value->id]['name'] = $value->name;
                $lists[$value->id]['discountFlag'] = $value->discountFlag;
                $lists[$value->id]['discountText'] = $value->discountText;
                $lists[$value->id]['discountRate'] = $value->discountRate;
                $lists[$value->id]['category_type'] = $value->category_type;
                $lists[$value->id]['date_start'] = $value->date_start;
                $lists[$value->id]['date_end'] = $value->date_end;
                $lists[$value->id]['place'] = $value->place;
                $lists[$value->id]['upload']['filename'][] = $value->filename;
                $lists[$value->id]['upload']['dispname'][] = $value->dispname;
                $lists[$value->id]['upload']['upload_id'][] = $value->upload_id;
                $i++;
            }
        }
        return $lists;
    }
    /******************
     * イベント次回のイベント情報取得カテゴリ別
     */
    public static function getEventListsSchedule()
    {
        $category_type = "";

        //次回のイベント一覧
        $now = date("Y-m-d");
        $events = Event::select([
            'events.id', 'events.sponser', 'events.name', 'events.discountFlag', 'events.discountText', 'events.discountRate','events.event_info', 'events.category_type', 'events.code', 'events.event_type', 'events.coworker', 'events.date_start', 'events.date_end', 'events.place', 'events.event_address', 'events.party', 'events.party_address', 'events.bank_name', 'events.bank_code', 'events.sanka_explain', 'events.other'
        ])
            ->where('events.status', 1)
            // ->where('events.date_start','>=', $now)
            ->orderBy('events.date_start', 'ASC')
            ->get();
        //データ配列の分割
        $lists = [];
        if (count($events)) {
            $i = 0;
            foreach ($events as $key => $value) {
                $lists[$value->id]['id'] = $value->id;
                $lists[$value->id]['sponser'] = $value->sponser;
                $lists[$value->id]['name'] = $value->name;
                $lists[$value->id]['discountFlag'] = $value->discountFlag;
                $lists[$value->id]['discountRate'] = $value->discountRate;
                $lists[$value->id]['discountText'] = $value->discountText;
                $lists[$value->id]['event_info'] = $value->event_info;
                $lists[$value->id]['category_type'] = $value->category_type;
                $lists[$value->id]['date_start'] = $value->date_start;
                $lists[$value->id]['date_end'] = $value->date_end;
                $lists[$value->id]['place'] = $value->place;
                $lists[$value->id]['event_address'] = $value->event_address;
                $lists[$value->id]['party'] = $value->party;
                $lists[$value->id]['party_address'] = $value->party_address;
                $lists[$value->id]['bank_name'] = $value->bank_name;
                $lists[$value->id]['bank_code'] = $value->bank_code;
                $lists[$value->id]['sanka_explain'] = $value->sanka_explain;
                $i++;
            }
        }
        return $lists;
    }

    /**********************
     * イベント取得code毎
     */
    public  function getEventDataCodeDateTerm($code)
    {
        $events = Event::where('events.status', 1)
            ->where("events.code", $code)
            ->first();
        $term = $this->getTermDate($events->date_start, $events->date_end);
        $this->event_id = $events->id;
        return $term;
    }

    /******************
     * 各イベント型の最新のイベントを取得
     */
    public static function getEventFirst()
    {
        $now = date("Y-m-d");
        $list = [];
        $events2 = Event::orderBy('date_start')->where('status', 1)
            ->where('date_end', '>=', $now)
            ->where('event_type', 1)
            ->where('category_type', 2)
            ->first();
        $list[2] = $events2;
        $events3 = Event::orderBy('date_start')->where('status', 1)
            ->where('date_end', '>=', $now)
            ->where('event_type', 1)
            ->where('category_type', 3)
            ->first();
        $list[3] = $events3;
        $events4 = Event::orderBy('date_start')->where('status', 1)
            ->where('date_end', '>=', $now)
            ->where('event_type', 1)
            ->where('category_type', 4)
            ->first();
        $list[4] = $events4;



        /*
        foreach($events as $key=>$val){
            $list[2] = $val;
        }
        */

        return $list;
    }
    public static function getEventMostNew()
    {
        $now = date("Y-m-d");
        $list = [];
        $events2 = Event::orderBy('date_start', 'DESC')->where('status', 1)
            ->where('event_type', 1)
            ->where('category_type', 2)
            ->first();
        $list[2] = $events2;
        $events3 = Event::orderBy('date_start', 'DESC')->where('status', 1)
            ->where('event_type', 1)
            ->where('category_type', 3)
            ->first();
        $list[3] = $events3;
        $events4 = Event::orderBy('date_start', 'DESC')->where('status', 1)
            ->where('event_type', 1)
            ->where('category_type', 4)
            ->first();
        $list[4] = $events4;



        /*
        foreach($events as $key=>$val){
            $list[2] = $val;
        }
        */

        return $list;
    }

    public static function getEventDataType($type)
    {
        $now = date("Y-m-d");
        //次回のイベント一覧
        $events = Event::where('status', 1)->where('category_type', $type)->where('date_end', '>=', $now)->orderBy('category_type', 'ASC')->orderBy('date_start', 'ASC')->get();
        return $events;
    }
    public static function getEventNumber($event_id)
    {
        //$event_number = Attendee::where('event_id',$event_id)->whereNull("deleted_at")->count()+1;
        $event_number = Attendee::where('event_id', $event_id)->max('event_number') + 1;
        return $event_number;
    }
}
