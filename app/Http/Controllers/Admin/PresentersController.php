<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Presenter;
use App\Models\FormDataPresenter;
use App\Models\FormInput;
use App\Models\Presentation;
use App\Models\User;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\FormInputValue;
use Illuminate\Validation\Rule;
use ProgramLists;

class PresentersController extends Controller
{
    private $category = '';
    private $form = '';
    private $presenter = '';
    private $attendee = '';
    private $attribute = [
        'attendee_id' => '参加者番号',
        'number' => '発表番号',
        'description' => '講演内容',
        'file.proceeding' => '原稿',
        'file.flash' => '原稿',
        'file.poster' => '原稿',
    ];

    public function __construct()
    {
        // ルートパラメータチェック
        $current = Route::current();
        if ($current) {

            // カテゴリータイプが不正な場合404エラー
            $category = config('pacd.category.' . $current->category_prefix);
            if (!$category || $category['prefix'] == 'members') {
                abort(404);
            }

            // 講演者IDがパラメータにある場合（更新、削除時）
            if ($current->presenter_id) {
                // 講演者登録されていない場合404エラー
                $this->presenter = Presenter::find($current->presenter_id);
                if (!$this->presenter) {
                    abort(404);
                }
                // URLのカテゴリータイプとイベントのカテゴリータイプが合っていいない場合404エラー
                $this->attendee = $this->presenter->attendee;
                if (!$this->attendee || $category['key'] != $this->attendee->event->category_type) {
                    abort(404);
                }
            }

            $this->form = config('pacd.form.type.' . $category['prefix'] . '_presenter');
            $this->category = $category;
        }
    }

    // 講演者一覧ページ表示
    public function index($category_prefix, Request $request)
    {
        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '一覧';
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_user_list' => 1])->get();
        $query = $this->getjoinList($request);

        $set['presenters'] = $query->paginate(20);

        $set[ 'search' ] = $request->search;
        $set[ 'code'   ] = $request->code;
        $set[ 'event_id'   ] = 0;
        if($request->code){
            $events = Event::where('code', $request->code)
                ->where('status',1)
                ->first();
            $set[ 'event_id'   ] = $events->id;
        }
        return view('admin.presenters.index', $set);
    }
    public function checked(Request $request){
        $code = "";
        if($request->code == 1) $code = "proceeding_flag";
        if($request->code == 2) $code = "flash_flag";
        if($request->code == 3) $code = "poster_flag";
        $flag = 0;
        if($request->flag == "true") $flag = 1;
        if(isset($request->id) && $request->id){
            Presentation::
            where("id",$request->id)
            ->whereNull('deleted_at')
            ->update([
                $code => $flag
            ]);
        }else{
            Presentation::
            whereNull('deleted_at')
            ->update([
                $code => $flag
            ]);
        }


        return true;
    }
    public function getjoinList($request){
        $query = Presenter::select(
            'presenters.*',
            'users.type as user_type',
            'users.type_number as user_type_number',
            'users.cp_name as user_cp_name',
            'users.postcode as user_postcode',
            'users.address as user_address',
            'users.busyo as user_busyo',
            'users.login_id as user_login_id',
            'users.sei as user_sei',
            'users.mei as user_mei',
            'users.sei_kana as user_sei_kana',
            'users.mei_kana as user_mei_kana',
            'users.group_flag as user_group_flag',
            'users.open_address_flag as user_open_address_flag',
            'users.tel as user_tel',
            'users.email as user_email',
            'events.name as event_name',
            'presentations.proceeding_flag as proceeding_flag',
            'presentations.flash_flag as flash_flag',
            'presentations.poster_flag as poster_flag',
            )
            ->where('events.category_type', $this->category['key'])
            ->leftJoin('attendees', 'attendees.id', '=', 'presenters.attendee_id')
            ->leftJoin('events', 'events.id', '=', 'attendees.event_id')
            ->leftJoin('presentations', 'presentations.presenter_id', '=', 'presenters.id')
            ->Join('users', 'attendees.user_id', '=', 'users.id');
        if ($request->code) {
            $query->where('events.code', $request->code);
        }
        $this->request = $request;
        if ($request->search) {
            $query = $query->where(function($query){
                $query = $query->orwhere('users.sei','like', '%'.$this->request->search.'%');
                $query = $query->orwhere('users.mei','like', '%'.$this->request->search.'%');
                $query = $query->orwhere('users.login_id','like', '%'.$this->request->search.'%');
                $query = $query->orwhere('presentations.number','like', '%'.$this->request->search.'%');
                $query = $query->orwhere('events.name','like', '%'.$this->request->search.'%');
            });
        }

        return $query;
    }

    // 講演者登録ページ表示
    public function create($category_prefix,Request $request)
    {
        $set = $this->getMeta();
        // 登録可能なイベントを取得
        $events = Event::where('category_type', $this->category['key'])
                    //->where('date_start', '>=', date('Y-m-d'))
                    ->where('date_end', '>=', date('Y-m-d'))
                    ->where('status',1)
                    ->orderBy('id', 'desc')
                    ->get();

        // 登録可能なイベントがない場合
        if ($events->count() === 0) {
            return redirect()->back()->with('flash.error', '登録可能なイベントがありません。<br>イベントを登録してください。');
        }

        $set['events'] = $events;
        $set['title'] = $this->form['display_name'] . '登録';
        $set['event_id'] = $request->event_id;
        //$set['inputs'] = FormInput::where(['form_type' => $this->form['key']])->get();

        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_published' => true])
        ->where('event_id', 0)
        ->orWhere(['event_id'=>$request->event_id])
        ->get();

        return view('admin.presenters.create', $set);
    }

    // 講演者登録処理（POST）
    public function store($category_prefix, Request $request)
    {

        $rules = [
            'event_id' => 'required|exists:events,id',
            'login_id' => ['required', 'exists:users,login_id', function($attribute, $value, $fail) use ($request) {
                $user = User::where('login_id', $value)->first();
                if ($user && !Attendee::where('event_id', $request->event_id)->where('user_id', $user->id)->first()) {
                    return $fail(' ');
                }
                return true;
            }],
            'attendee_id' => 'required|exists:attendees,id',
            'number' => 'required|alpha-dash-check|max:255',
            'description' => 'nullable|string',
            'file.*' => 'nullable|file',
        ];
        if ($request->custom) {
            $rules['custom.*'] = new CustomFormDataRule();
        }

        $request->validate($rules, [], $this->attribute);



        $attendees_data_id = Attendee::select('id')->where('event_id',$request->event_id)->get();
        $attendees_idline = [];
        foreach($attendees_data_id as $value){
            $attendees_idline[] = $value->id;
        }

        $presenters_data_id = Presenter::select('id')->whereIn('attendee_id',$attendees_idline)->get();
        $presenters_idline = [];

        foreach($presenters_data_id as $value){
            $presenters_idline[] = $value->id;
        }

        $presentations_data = Presentation::whereIn('presenter_id',$presenters_idline)->where('number',$request->number)->get();


        if(count($presentations_data)){
            return redirect()->back()->withInput()->with('flash.error', $this->form['display_name'] . '既に使われている発表番号です。');
            exit();
        }

        /*
        $request->validate([
            'number'=>[Rule::unique('presentations','number')->whereNull('deleted_at')]
        ],
        [
            'number'=>"既に使われている発表番号です。"
        ],
        [
            'number'=>"発表番号"
        ]);
        */


        DB::beginTransaction();
        try {
            $presenter = Presenter::create(['attendee_id' => $request->attendee_id]);
            $request->merge(['presenter_id' => $presenter->id]);
            if ($request->file) {
                $dir = config('pacd.presentation_file.path') . '/' . $this->category['prefix'] . '/' . $request->number."-".$presenter->id;
                foreach ($request->file as $type => $file) {
                    $path = Storage::putFileAs($dir, $file, $file->getClientOriginalName());
                    $request->merge([$type => $path]);
                }
            }
            $presentation = Presentation::create($request->all());
            if ($request->custom) {
                FormDataPresenter::createFromInputData($request->custom, $presenter);
            }
            DB::commit();
            Log::info("【管理者：" . $this->form['display_name']. "登録】presenter_id:$presenter->id, presentation_id:$presentation->id");

            // TODO: 講演者登録メール送信
            if ($request->send_mail) {
                $this->category_prefix = $category_prefix;
                $user = User::where('login_id', $request->login_id)->first();
                $this->mailsend($user->id,$presenter->id);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：" . $this->form['display_name']. "登録】attendee_id: " . $request->attendee_id . " error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('flash.error', '講演者の登録に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', '講演者を登録しました。');
    }

    //メール配信
    public function mailsend($userid,$presenter_id){

        //例会参加メール取得
        if($this->category_prefix == "kosyukai"){
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.kosyukai.8.key');
        }else
        if($this->category_prefix == "touronkai"){
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.touronkai.5.key');
        }else{
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.reikai.2.key');
        }
        $mailformat = Mailforms::getData($form_type);

        //ユーザーデータ取得
        $userdata = User::where("id",$userid)->first();
        $this->to = $userdata->email;
        //タイトルの置き換え
        $title = $mailformat->title ?? '';
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $title = preg_replace("/".$value['replace']."/",$userdata->$key,$title);
        }
        $this->title = $title;
        //本文の置き換え
        $body = $mailformat->note;
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $body = preg_replace("/".$value['replace']."/",$userdata->$key,$body);
        }

        if($this->category_prefix == "kosyukai"){
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.kosyukai_presenter.key')])->get();
        }else
        if($this->category_prefix == "touronkai"){
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.touronkai_presenter.key')])->get();
        }else{
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.reikai_presenter.key')])->get();
        }


        foreach($forminput as $key=>$value){
            //登録フォームの値

            $forminputvalue = FormInputValue::where(['form_input_id'=>$value->id])->get();
            $write = "";
            foreach($forminputvalue as $k=>$val){
                $form_data_attendees = FormDataPresenter::where(['presenter_id'=>$presenter_id])
                ->where(["form_input_value_id"=>$val->id])
                ->first();

                if(!$form_data_attendees['data']) continue;
               $write .= $form_data_attendees['data'].$form_data_attendees[ 'data_sub' ]."\n";

            }
            $body = preg_replace("/##mem".$value['id']."##/",$write,$body);

        }
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        $admin = config("admin.email");
        $head = config("admin.head");

        $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
        $header.="\n";
        $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
        $pfrom = "-f$admin";

        mb_send_mail($this->to , $this->title , $body,$header,$pfrom);

        return true;

        /*
        //メール配信
        Mail::raw($body, function ($message) {
            $message->to($this->to)
                ->subject($this->title);
        });
        */

    }

    // 講演者情報編集ページ表示
    public function edit($category_prefix, $id)
    {
        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '編集';
        $set['presenter'] = $this->presenter;
        $set['presentation'] = $this->presenter->presentation;
        $set['attendee'] = $this->attendee;
        $set['user'] = $this->attendee->user;
        $set['event'] = $this->attendee->event;
        $set['event_id'] = $this->attendee->event_id;
        //$set['inputs'] = FormInput::where(['form_type' => $this->form['key']])->get();
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_published' => true])
        ->where('event_id', 0)
        ->orWhere(['event_id'=>$this->attendee->event_id])
        ->get();
        return view('admin.presenters.edit', $set);
    }

    // 情報更新処理（PUT/PATCH）
    public function update($category_prefix, $id, Request $request)
    {
        $rules = [
            'description' => 'nullable|string',
          //  'file.*' => 'nullable|file',
            'delete.*' => 'nullable|boolean',
        ];
        $presentation = Presentation::firstOrNew(['presenter_id' => $id]);
        $presenter = Presenter::firstOrNew(['id' => $id]);
        $attendee = Attendee::firstOrNew(['id' => $presenter->attendee_id]);

        $attendees_data_id = Attendee::select('id')->where('event_id',$attendee->event_id)->get();
        $attendees_idline = [];
        foreach($attendees_data_id as $value){
            $attendees_idline[] = $value->id;
        }

        $presenters_data_id = Presenter::select('id')->whereIn('attendee_id',$attendees_idline)->where('id',"!=",$presenter->id)->get();
        $presenters_idline = [];
        foreach($presenters_data_id as $value){
            $presenters_idline[] = $value->id;
        }

        $presentations_data = Presentation::whereIn('presenter_id',$presenters_idline)->where('number',$request->number)->get();
        // var_dump($presentations_data);
        // exit();
        // var_dump($attendee->event_id);
       // $rules['number'] = ['required', 'alpha-dash-check', 'max:255'];
        if ($presentation->number != $request->number) {
        //      $rules['number'][] = 'unique:presentations';
        }
        if ($request->custom) {
            $rules['custom.*'] = new CustomFormDataRule();
        }


        $request->validate($rules, [], $this->attribute);

        if ($request->number && $presentation->number != $request->number) {
            $presentations = Presentation::select("attendees.event_id")
            ->leftJoin('presenters', 'presenters.id', '=', 'presentations.presenter_id')
            ->leftJoin('attendees', 'attendees.id', '=', 'presenters.attendee_id')
            ->where([
                'number' =>$request->number,
                'event_id' =>$attendee->event_id
            ])
            ->count();

            if($presentations > 0 ){
                /*
                $request->validate([
                    'number'=>[
                    Rule::unique('presentations','number')
                    ->whereNull('deleted_at')
                    ]
                ],
                [
                    'number.unique'=>"既に使われている発表番号です。"
                ],
                [
                    'number'=>"発表番号"
                ]);
                */
                if(count($presentations_data)){
                    return redirect()->back()->withInput()->with('flash.error', $this->form['display_name'] . '既に使われている発表番号です。');
                    exit();
                }

            }

        }
        DB::beginTransaction();
        try {
            if ($request->custom) {
                FormDataPresenter::updateFromInputData($request->custom, $this->presenter);
            }
            $dir = config('pacd.presentation_file.path') . '/' . $this->category['prefix'] . '/' . $request->number.'-'.$presenter->id;

            foreach (config('pacd.presentation_file.type') as $type) {
                if (@$request->delete[$type] && $presentation->$type) {
                    // 削除
                    Storage::delete($presentation->$type);
                    $presentation->$type = '';
                } elseif (@$request->file[$type]) {
                    $file = $request->file("file.$type");
                    if ($file) {
                        // アップロード
                        if ($presentation->$type) Storage::delete($presentation->$type); // 登録済みファイル削除
                        $path = Storage::putFileAs($dir, $file, $file->getClientOriginalName());
                        $request->merge([$type => $path]);
                    }
                }
            }
            $presentation->fill($request->all());
            $presentation->save();
            DB::commit();
            Log::info("【管理者：" . $this->form['display_name']. "情報編集】presenter_id:$id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：" . $this->form['display_name'] . "情報編集】presenter_id: $id, error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('flash.error', $this->form['display_name'] . '情報の更新に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', $this->form['display_name'] . '情報を更新しました。');
    }

    // 削除処理（DELETE）
    public function destroy($category_prefix, $id)
    {

        DB::beginTransaction();
        try {

            $presentation = Presentation::where('presenter_id', $this->presenter->id)->first();
            if (!empty($presentation)) {
                $presentation->delete();
            }

            $this->presenter->delete();
            DB::commit();
            //Log::info("【管理者：" . $this->form['display_name']. "削除】presenter_id:$id, presentation_id:$presentation->id");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：" . $this->form['display_name'] . "削除】presenter_id:$id, error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('flash.error', $this->form['display_name']. 'の削除に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', $this->form['display_name'] . 'を削除しました。');
    }




    // 一覧ページにリダイレクト
    private function redirectIndex() {
        $events = "";
        if(isset($this->attendee->event)){
            $events = $this->attendee->event;
        }
        if(isset($events->code) && $events->code){
            return redirect(route('admin.presenters.index', [$this->form['category_prefix']])."/?code=".$events->code);
        }else{
            return redirect(route('admin.presenters.index', [$this->form['category_prefix']]));

        }
    }

    // メタデータを返す
    private function getMeta() {
        return [
            'breadcrumbs' => [
                [
                    'title' => $this->form['display_name'] . '一覧',
                    'url' => route('admin.presenters.index', [$this->form['category_prefix']]),
                ]
            ],
            'form' => $this->form
        ];
    }


    //CSVダウンロード
    public function csvdownload(Request $request){
        $master_array_type = config('pacd.user.type');
        //配列の並び順指定
        $array_type[2] = $master_array_type[2];
        $array_type[3] = $master_array_type[3];
        $array_type[4] = $master_array_type[4];
        $array_type[1] = $master_array_type[1];
        $array_type[5] = $master_array_type[5];
        $array_type[6] = $master_array_type[6];

        $payment = config('pacd.payment');

        $query = $this->getjoinList($request);
        $query = $query->get();

        $data = [];
        foreach($query as $key=>$value){
            $user = $value->user_type;
            $data[$value->user_type][] = $value;
        }

        // フォーム取得
        $sql = "SELECT
                *
            FROM
                form_inputs as fi
            LEFT JOIN form_input_values as fiv ON fi.id = fiv.form_input_id
            WHERE
                fi.form_type=?
            order by fi.id
            ";

        $temphead = DB::select($sql,[$this->form['key']]);
        $num = 0;
        for($i=0;$i<count($temphead);$i++){
            $temphead[$i]->sortNum = $num;

            if(isset($temphead[$i+1]) && $temphead[$i]->form_input_id != $temphead[$i+1]->form_input_id){
                $num = 0;
            }else{
                $num++;

            }
        }


        // 参加者事に選択したカスタムフォームの選択値
        $sql = "SELECT
                fi.id as form_input_id,
                fdp.presenter_id,
                fdp.data
            FROM
                form_inputs as fi
            LEFT JOIN form_input_values as fiv ON fi.id = fiv.form_input_id
            LEFT JOIN form_data_presenters as fdp ON fdp.form_input_value_id = fiv.id
            WHERE
                fi.form_type=?
            order by fi.id
            ";

        $tempdata = DB::select($sql,[$this->form['key']]);



        $viewData = [];
        foreach($tempdata as $value){
            $viewData[$value->presenter_id][$value->form_input_id][] = $value->data;
        }



        if($this->form['category_prefix'] == "touronkai"){
            //法人会員(担当窓口）
            $header[2] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","法人会員番号","法人名","郵便番号","法人住所","所属部署","担当者氏名","担当者氏名かな","担当者電話番号","担当者メールアドレス","メールアドレス公開"];

            $header[3] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","法人会員番号","法人名","郵便番号","法人住所","所属部署","担当者氏名","担当者氏名かな","担当者電話番号","担当者メールアドレス","メールアドレス公開"];

            $header[4] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","個人会員番号","郵便番号","所属住所","所属","氏名","ふりがな","電話番号","メールアドレス","メールアドレス公開"];

            $header[1] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","郵便番号","所属住所","所属","氏名","ふりがな","協賛学会所属の有無","電話番号","メールアドレス"];

            $header[5] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","郵便番号","所属住所","所属","氏名","ふりがな","協賛学会所属の有無","電話番号","メールアドレス"];
            
            $header[6] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","郵便番号","所属住所","所属","氏名","ふりがな","協賛学会所属の有無","電話番号","メールアドレス"];
        }else{
            //法人会員(担当窓口）
            $header[2] = ["参加イベント","発表番号","講演者番号","講演内容","参加者番号","ID","会員種別","法人会員番号","法人名","郵便番号","法人住所","所属部署","担当者氏名","担当者氏名かな","担当者電話番号","担当者メールアドレス","メールアドレス公開"];

            $header[3] = ["参加イベント","発表番号","講演者番号","講演内容","参加者番号","ID","会員種別","法人会員番号","法人名","郵便番号","法人住所","所属部署","担当者氏名","担当者氏名かな","担当者電話番号","担当者メールアドレス","メールアドレス公開"];

            $header[4] = ["参加イベント","発表番号","講演者番号","講演内容","参加者番号","ID","会員種別","個人会員番号","郵便番号","所属住所","所属","氏名","ふりがな","電話番号","メールアドレス","メールアドレス公開"];

            $header[1] = ["参加イベント","発表番号","講演者番号","講演内容","参加者番号","ID","会員種別","郵便番号","所属住所","所属","氏名","ふりがな","協賛学会所属の有無","電話番号","メールアドレス"];

            $header[5] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","郵便番号","所属住所","所属","氏名","ふりがな","協賛学会所属の有無","電話番号","メールアドレス"];
            
            $header[6] = ["参加イベント","発表番号","講演者番号","題目","講演演者","所属","発表概要","参加者番号","ID","会員種別","郵便番号","所属住所","所属","氏名","ふりがな","協賛学会所属の有無","電話番号","メールアドレス"];
        }

        // ヘッダにカスタム項目の追加
        for($i=1;$i<=count($header);$i++){
            foreach($temphead as $value){
               @array_push($header[$i],$value->name);
            }
        }

        $users=[];
        foreach($array_type as $keys=>$values){
            if(isset($header[$keys])){
                $users[] = [$values];
                $users[] = $header[$keys];
                if(isset($data[$keys])){
                    foreach($data[$keys] as $key=>$value){

                        $num = sprintf('%010d', $value->event_number);
                        $attendee = $value->attendee;
                        $presentation = $value->presentation;

                        $clum = [];
                        $clum[] = $value->event_name;
                        $clum[] = isset($presentation->number)?$presentation->number:"";
                        $clum[] = "=\"".sprintf("%010d",$value->id)."\"";
                        if($this->form['category_prefix'] == "touronkai"){
                            $clum[] = isset($presentation->daimoku) ? htmlspecialchars($presentation->daimoku):"";
                            $clum[] = isset($presentation->enjya) ? htmlspecialchars($presentation->enjya):"";
                            $clum[] = isset($presentation->syozoku) ? htmlspecialchars($presentation->syozoku):"";
                            $clum[] = isset($presentation->gaiyo) ? htmlspecialchars($presentation->gaiyo):"";
                        }else{
                            $clum[] = isset($presentation->description) ? htmlspecialchars($presentation->description):"";
                        }

                        if(isset($attendee->event_number)){
                            $clum[] = "=\"".sprintf("%010d",$attendee->event_number)."\"";
                        }else{
                            $clum[] = "=\\";

                        }
                        $clum[] = $value->user_login_id;
                        $clum[] = $array_type[$value->user_type];
                        if($value->user_type == 2 ) $clum[] = $value->user_type_number;
                        if($value->user_type == 3 ) $clum[] = $value->user_type_number;
                        if($value->user_type == 4 ) $clum[] = $value->user_type_number;
                        if($value->user_type == 1 ) $clum[] = $value->user_type_number;

                        if($value->user_type == 2) $clum[] = $value->user_cp_name;
                        if($value->user_type == 3) $clum[] = $value->user_cp_name;
                        if($value->user_type == 4) $clum[] = $value->user_postcode;
                        if($value->user_type == 1) $clum[] = $value->user_address;


                        if($value->user_type == 2) $clum[] = $value->user_postcode;
                        if($value->user_type == 3) $clum[] = $value->user_postcode;
                        if($value->user_type == 4) $clum[] = $value->user_address;
                        if($value->user_type == 1) $clum[] = $value->user_busyo;


                        if($value->user_type == 2) $clum[] = $value->user_address;
                        if($value->user_type == 3) $clum[] = $value->user_address;
                        if($value->user_type == 4) $clum[] = $value->user_busyo;
                        if($value->user_type == 1) $clum[] = $value->user_sei.$value->user_mei;


                        if($value->user_type == 2) $clum[] = $value->user_busyo;
                        if($value->user_type == 3) $clum[] = $value->user_busyo;
                        if($value->user_type == 4) $clum[] = $value->user_sei.$value->user_mei;
                        if($value->user_type == 1) $clum[] = $value->user_sei_kana.$value->user_mei_kana;


                        if($value->user_type == 2) $clum[] = $value->user_sei.$value->user_mei;
                        if($value->user_type == 3) $clum[] = $value->user_sei.$value->user_mei;
                        if($value->user_type == 4) $clum[] = $value->user_sei_kana.$value->user_mei_kana;
                        if($value->user_type == 1) $clum[] = $value->user_group_flag;


                        if($value->user_type == 2) $clum[] = $value->user_sei_kana.$value->user_mei_kana;
                        if($value->user_type == 3) $clum[] = $value->user_sei_kana.$value->user_mei_kana;
                        if($value->user_type == 4) $clum[] = $value->user_tel;
                        if($value->user_type == 1) $clum[] = $value->user_tel;



                        if($value->user_type == 2) $clum[] = $value->user_tel;
                        if($value->user_type == 3) $clum[] = $value->user_tel;
                        if($value->user_type == 4) $clum[] = $value->user_email;
                        if($value->user_type == 1) $clum[] = $value->user_email;



                        if($value->user_type == 2) $clum[] = $value->user_email;
                        if($value->user_type == 3) $clum[] = $value->user_email;
                        if($value->user_type == 4) $clum[] = config('pacd.open_address_flag')[$value->user_open_address_flag];


                        if($value->user_type == 2) $clum[] = config('pacd.open_address_flag')[$value->user_open_address_flag];
                        if($value->user_type == 3) $clum[] = config('pacd.open_address_flag')[$value->user_open_address_flag];

                        /*
                        if(isset($tmpValueLists[$value->id])){
                           $ex = explode(",",$tmpValueLists[$value->id]);
                           foreach($ex as $exp){
                            $clum[] = $exp;
                           }
                        }
                        */
                        $no = 0;
                        foreach($temphead as $head){
                            //$clum[] = $value->id."-".$head->form_input_id."-".$head->sortNum;
                            if(isset($viewData[ $value->id] )){
                                $clum[] = @$viewData[$value->id][$head->form_input_id][$head->sortNum];
                            }else{
                                $clum[] = "";
                            }
                            //$clum[] = $value->id."-".$head->form_input_id;
                            // if(isset($viewData[$value->id][$head->form_input_id])){
                            //     foreach($viewData[$value->id][$head->form_input_id] as $v){
                            //         $clum[] = $v;
                            //     }
                            // }

                        }

                        $users[] = $clum;
                    }
                }
                $users[] = [];
            }
        }

        $f = fopen('test.csv', 'w');
        foreach ($users as $user) {
            $user = mb_convert_encoding($user, 'SJIS-win', 'UTF-8');
            fputcsv($f, $user);
        }
        $filename = date('YmdHis').".csv";

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename,
        );

        fclose($f);
        // HTTPヘッダ
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename='.$filename);
        readfile("test.csv");

        exit();

    }

    // 資料一括ダウンロード
    // NOTE:現状技術講習会のみこの機能を使用
    public function filedownload(Request $request){
        // 技術講習会以外の場合404エラー
        if ($this->category['prefix'] !== config("pacd.category.kosyukai.prefix")) {
            abort(404);
        }

        // 検索条件に合致する講演者のファイル情報取得
        $data = [];
        $query = $this->getjoinList($request);
        foreach ($query->get() as $presenter) {
            if ($presentation = $presenter->presentation) {
                if ($presentation->proceeding) $data[] = $presentation->proceeding;
                if ($presentation->flash) $data[] = $presentation->flash;
                if ($presentation->poster) $data[] = $presentation->poster;
            }
        }

        //からフォルダの作成
        $basepath = public_path()."/pdf/";
        $folder = $basepath.date('Ymdhis');
        mkdir($folder, 0777, true);
        $filepath = storage_path('app')."/";
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        for($i=0;$i<count($data);$i++) {
            $path = "";
            if($data){
                if($data[$i]) $path = $data[$i];
                $dir = $filepath.dirname($path)."/";
                $basename = basename($path);
                if(pathinfo($basename, PATHINFO_FILENAME)){
                    @copy($dir.$basename,$folder."/".$basename);
                }
            }
        }

        $fileName = "zipFile1".date('YmdHis');
        // if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // windows用zipコマンド
            // $command = "powershell compress-archive {$folder}/* {$basepath}{$fileName}";
        // } else {
            $command =  "cd ". $folder .";"."zip -r ". $basepath . $fileName .".zip .";
        // }

        exec($command);

        mb_http_output( "pass" );
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");

        header('Content-Disposition: attachment; filename*=UTF-8\'\'' . $fileName.".zip");
        ob_end_clean();
        readfile($basepath.$fileName.".zip");
        exit();
    }
}
