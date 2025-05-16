<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Event_join;
use App\Models\FormDataAttendee;
use App\Models\FormInput;
use App\Models\User;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\FormInputValue;
use App\Models\Presenter;
use App\Models\Presentation;
use App\Models\kyosanTitle;
use Response;

use function GuzzleHttp\Promise\all;

class AttendeesController extends Controller
{
    private $category = '';
    private $form = '';
    private $attendee = '';
    private $attribute = [
        'event_id' => '参加イベント',
        'login_id' => 'ログインID',
        'is_paid' => '参加費支払い状況',
        //    'event_join_id' => '参加料金',
    ];

    public function __construct()
    {
        // ルートパラメータチェック
        $current = Route::current();
        if ($current && $current->getActionMethod() != 'get_user_attendee') {

            // カテゴリータイプが不正な場合404エラー
            $category = config('pacd.category.' . $current->category_prefix);
            if (!$category || $category['prefix'] == 'members') {
                abort(404);
            }

            // 参加者IDがパラメータにある場合（更新、削除時）
            if ($current->attendee_id) {
                // 参加者登録されていない場合404エラー
                // URLのカテゴリータイプとイベントのカテゴリータイプが合っていいない場合404エラー
                $this->attendee = Attendee::find($current->attendee_id);
                if (!$this->attendee || $category['key'] != $this->attendee->event->category_type) {
                    abort(404);
                }
            }

            $this->form = config('pacd.form.type.' . $category['prefix'] . '_attendee');
            $this->category = $category;
        }
    }

    // 参加者一覧ページ表示
    public function index($category_prefix, Request $request)
    {

        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '一覧';
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_user_list' => 1])->get();
        $events = Event::where('category_type', $this->category['key'])->where('status', 1)->get();
        $query = $this->getjoinList($request);
        $set['all_attendees'] = $query->get();
        $set['attendees'] = $query->paginate(10);


        $set['search'] = $request->search;
        $set['code'] = $request->code;
        $set['events'] = $events;
        return view('admin.attendees.index', $set);
    }
    public function getjoinList($request)
    {
        $query = Attendee::select([
            'attendees.*', 'users.login_id'
        ])
            ->selectRaw("(CASE attendees.join_status WHEN 0 THEN '" . config('pacd.join_status')[1] . "' WHEN 1 THEN '" . config('pacd.join_status')[0] . "' END) AS join_code")
            ->where('events.category_type', $this->category['key'])
            ->leftJoin('events', 'events.id', '=', 'attendees.event_id')
            ->Join('users', 'attendees.user_id', '=', 'users.id');
        if ($request->code) {
            $query->where('events.code', $request->code);
        }
        if ($request->event_id) {
            $query->where('attendees.event_id', $request->event_id);
        }
        $this->request = $request;
        if ($request->search) {
            $query = $query->where(function ($query) {
                $search = preg_replace("/ /", "", $this->request->search);
                $query = $query->orwhere('users.cp_name', 'like', '%' . $this->request->search . '%');
                $query = $query->orwhere(DB::raw('CONCAT(users.sei,users.mei)'), 'like', '%' . $search . '%');
                $query = $query->orwhere('users.login_id', 'like', '%' . $search . '%');
                $query = $query->orwhere('users.email', 'like', '%' . $search . '%');
                $query = $query->orwhere('users.cp_name', 'like', '%' . $search . '%');
                $query = $query->orwhere('users.busyo', 'like', '%' . $search . '%');
                // $query = $query->orwhere('events.name','like', '%'.$search.'%');
                if (preg_match("/^[a-zA-Z0-9]+$/", $search)) {
                    $query = $query->orwhere('attendees.event_number', 'like', '%' . (int)$search . '%');
                }
            });
        }

        // $query = $query->where('events.status',1);
        //  $query = $query->where('events.enabled',1);
        $query = $query->where('users.deleted_at', null);

        return $query;
    }
    // 参加者登録ページ表示
    public function create($category_prefix)
    {
        $set = $this->getMeta();

        // 登録可能なイベントを取得
        $events = Event::where('category_type', $this->category['key'])
            //->where('date_start', '>=', date('Y-m-d'))
            ->where('date_end', '>=', date('Y-m-d'))
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();
        // 登録可能なイベントがない場合
        if ($events->count() === 0) {
            return redirect()->back()->with('flash.error', '登録可能なイベントがありません。<br>イベントを登録してください。');
        }

        $set['category_prefix'] = $category_prefix;
        $set['events'] = $events;
        $set['kyosanTitle'] = kyosanTitle::first();
        $set['title'] = $this->form['display_name'] . '登録';
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key']])->get();

        return view('admin.attendees.create', $set);
    }

    // 参加者登録処理（POST）
    public function store($category_prefix, Request $request)
    {
        $rules = [
            'event_id' => 'required|exists:events,id',
            'login_id' => ['required', 'exists:users,login_id', function ($attribute, $value, $fail) use ($request) {
                $user = User::where('login_id', $value)->first();
                if ($user && Attendee::where('event_id', $request->event_id)->where('user_id', $user->id)->first()) {
                    return $fail(' ');
                }
                return true;
            }],
            'is_paid' => 'required|boolean',
            'is_enabled_invoice' => 'required|boolean',
            //     'event_join_id' => 'nullable|exists:event_joins,id',
            'send_mail' => 'nullable|boolean',
        ];
        // if ($request->custom) {
        //     $rules['custom.*'] = new CustomFormDataRule();
        // }


        $request->validate($rules, [], $this->attribute);

        DB::beginTransaction();
        try {
            $user = User::where('login_id', $request->login_id)->first();
            $request->merge(['user_id' => $user->id]);

            if (isset($request->event_join_id) && count($request->event_join_id) > 0) {
                $request->merge(['event_join_id_list' => implode(",", $request->event_join_id)]);
            } else {
                $request->merge(['event_join_id_list' => ""]);
            }
            //event_numberの取得
            $request->merge(['event_number' => Event::getEventNumber($request->event_id)]);
            //event_join_idは使わないので空欄にする
            unset($request['event_join_id']);

            $attendee = Attendee::create($request->all());
            if ($request->custom) {
                FormDataAttendee::createFromInputData($request->custom, $attendee);
            }
            DB::commit();
            Log::info("【管理者：" . $this->form['display_name'] . "登録】attendee_id:$attendee->id");

            // TODO: 参加者登録メール送信
            if ($request->send_mail) {
                $this->category_prefix = $category_prefix;
                $this->mailsend($user->id, $request);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：" . $this->form['display_name'] . "登録】error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('flash.error', '参加者の登録に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', '参加者を登録しました。');
    }

    //メール配信
    public function mailsend($userid, $request)
    {

        //例会参加メール取得
        if ($this->category_prefix == "kosyukai") {
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.kosyukai.7.key');
        } else
        if ($this->category_prefix == "touronkai") {
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.touronkai.4.key');
        } elseif ($this->category_prefix == "kyosan") {
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.kyosan.17.key');
        } else {
            $form_type = config('pacd.CONST_MAIL_FORM_TEMP.reikai.1.key');
        }

        $mailformat = Mailforms::getData($form_type);

        //ユーザーデータ取得
        $userdata = User::where("id", $userid)->first();

        $this->to = $userdata->email;
        //タイトルの置き換え
        $title = $mailformat->title ?? '';
        foreach (config('pacd.CONST_MAIL_REPLACE.member') as $key => $value) {
            $title = preg_replace("/" . $value['replace'] . "/", $userdata->$key, $title);
        }
        $this->title = $title;
        //本文の置き換え
        $body = $mailformat->note;
        $attendee = Attendee::where(['event_id' => $request->event_id, 'user_id' => $userid])->first();
        $body = preg_replace("/##paydate##/", $attendee->paydate, $body);
        //参加料金
        if (!empty($attendee->event_join_id)) {
            $event_joins = Event_join::where([
                "id" => $attendee->event_join_id, "status" => 1, "join_status" => 1
            ])->first();
            $body = preg_replace("/##joinname##/", $event_joins->join_name, $body);
            $body = preg_replace("/##joinprice##/", $event_joins->join_price, $body);
            $body = preg_replace("/##joinfee##/", $event_joins->join_fee, $body);
        }
        foreach (config('pacd.CONST_MAIL_REPLACE.member') as $key => $value) {
            $body = preg_replace("/" . $value['replace'] . "/", $userdata->$key, $body);
        }

        if ($this->category_prefix == "kosyukai") {
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.kosyukai_attendee.key')])->get();
        } else
        if ($this->category_prefix == "touronkai") {
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.touronkai_attendee.key')])->get();
        } else
        if ($this->category_prefix == "kyosan") {
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.kyosan_attendee.key')])->get();
        } else {
            $forminput = FormInput::where(['form_type' => config('pacd.form.type.reikai_attendee.key')])->get();
        }


        foreach ($forminput as $key => $value) {
            //登録フォームの値
            $forminputvalue = FormInputValue::where(['form_input_id' => $value->id])->get();
            $write = "";
            foreach ($forminputvalue as $k => $val) {

                $form_data_attendees = FormDataAttendee::where(['attendee_id' => $attendee->id])
                    ->where(["" => $val->id])
                    ->first();
                if (!$form_data_attendees['data']) continue;
                $write .= $form_data_attendees['data'] . $form_data_attendees['data_sub'] . "\n";
            }
            $body = preg_replace("/##mem" . $value['id'] . "##/", $write, $body);
        }
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");


        $admin = config("admin.email");
        $head = config("admin.head");

        $header = "From: " . mb_encode_mimeheader($head) . "<" . $admin . ">";
        $header .= "\n";
        $header .= "Bcc:" . mb_encode_mimeheader("管理者") . "<" . $admin . ">";
        $pfrom = "-f$admin";


        if (config("app.env") === "local") {
            //メール配信
            Mail::raw($body, function ($message) {
                $message->to($this->to)
                    ->subject($this->title);
            });
        } else {
            // 本番用
            mb_send_mail($this->to, $this->title, $body, $header, $pfrom);
        }
        return true;
    }


    // 参加者情報編集ページ表示
    public function edit($category_prefix, $id)
    {

        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '編集';
        $set['attendee'] = $this->attendee;
        $set['user'] = $this->attendee->user;
        $set['event'] = $this->attendee->event;
        $set['event_joins_status'] = $this->setJoinStatus($this->attendee->event_id);
        $set['kyosanTitle'] = kyosanTitle::first();
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key']])->get();
        return view('admin.attendees.edit', $set);
    }
    //
    public function setJoinStatus($event_id){

        $event_joins_status3 = Event_join::
            select(['join_status','join_name','number'])
            ->where([
                "event_id" => $event_id, "status" => 1, "pattern" => 3, "join_status"=>1
            ])->first();
        $event_joins_status4 = Event_join::
            select(['join_status','join_name','number'])
            ->where([
                "event_id" => $event_id, "status" => 1, "pattern" => 4, "join_status"=>1
            ])->first();
        $event_joins_status[3] =  $event_joins_status3;
        $event_joins_status[4] =  $event_joins_status4;
        return $event_joins_status;
    }

    // 情報更新処理（PUT/PATCH）
    public function update($category_prefix, $id, Request $request)
    {
        $rules['is_paid'] = 'required|boolean';
        $rules['is_enabled_invoice'] = 'required|boolean';
        // if ($request->event_join_id) {
        //    $rules['event_join_id'] = 'nullable|exists:event_joins,id';
        // }
        if ($request->custom) {
            $rules['custom.*'] = new CustomFormDataRule();
        }
        //$request->validate($rules);

        DB::beginTransaction();
        try {
            //event_join_idは使わないので空欄にする
            if (isset($request->event_join_id) && count($request->event_join_id) > 0) {
                $request->merge(['event_join_id_list' => implode(",", $request->event_join_id)]);
            } else {
                $request->merge(['event_join_id_list' => ""]);
            }
            unset($request['event_join_id']);

            if ($category_prefix === "kyosan") {
                $this->attendee->paydate = $request->paydate;
            }

            if(!$request['tenjiSanka1Status']) $request['tenjiSanka1Status'] = "";
            if(!$request['tenjiSanka2Status']) $request['tenjiSanka2Status'] = "";
            if(!$request['konsinkaiSanka1Status']) $request['konsinkaiSanka1Status'] = "";
            if(!$request['konsinkaiSanka2Status']) $request['konsinkaiSanka2Status'] = "";
            
            $this->attendee->fill($request->all());
            $this->attendee->save();
            if ($request->custom) {
                FormDataAttendee::updateFromInputData($request->custom, $this->attendee);
            }
            DB::commit();
            Log::info("【管理者：" . $this->form['display_name'] . "情報編集】attendee_id:$id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：" . $this->form['display_name'] . "情報編集】attendee_id: $id, error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('flash.error', $this->form['display_name'] . '情報の更新に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', $this->form['display_name'] . '情報を更新しました。');
    }

    // 削除処理（DELETE）
    public function destroy($category_prefix, $id)
    {
        //削除した際に講演者情報の削除も行う
        $date = date('Y-m-d H:i:s');
        $pres = Presenter::where('attendee_id', $id)->get();
        foreach ($pres as $key => $value) {
            Presentation::where('presenter_id', $value->id)->update(['deleted_at' => $date]);
        }
        Presenter::where('attendee_id', $id)->update(['deleted_at' => $date]);


        DB::beginTransaction();
        try {
            $this->attendee->delete();
            DB::commit();
            Log::info("【管理者：" . $this->form['display_name'] . "削除】attendee_id:$id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：" . $this->form['display_name'] . "削除】attendee_id:$id, error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('flash.error', $this->form['display_name'] . 'の削除に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', $this->form['display_name'] . 'を削除しました。');
    }

    // 会員情報及び参加者情報を配列で返す（POST）
    public function get_user_attendee($login_id, $event_id = '')
    {
        $user = User::Where('login_id', $login_id)->first();
        $data = [];
        if ($user) {
            $data = $user->toArray();
            $attendee = Attendee::where('event_id', $event_id)->where('user_id', $user->id)->first();
            $data['attendee_id'] = $attendee ? $attendee->id : false;
            $data['event_number'] = $attendee ? $attendee->event_number : false;
        }
        return $data ?? false;
    }




    // 一覧ページにリダイレクト
    private function redirectIndex()
    {
        return redirect(route('admin.attendees.index', [$this->form['category_prefix']]));
    }

    // メタデータを返す
    private function getMeta()
    {

        return [
            'breadcrumbs' => [
                [
                    'title' => $this->form['display_name'] . '一覧',
                    'url' => route('admin.attendees.index', [$this->form['category_prefix']]),
                ]
            ],
            'form' => $this->form
        ];
    }
    public function invoiceStatusDownload($category_prefix, $sts, $code = "")
    {

        if ($category_prefix == "reikai") {
            $temp = Event::where("category_type", 2)->where("status", 1);
            if ($code) {
                $temp->where('code', $code);
            }
            $tmpdata = $temp->get();
            foreach ($tmpdata as $value) {
                $flg = Attendee::where('event_id', $value->id)->where("deleted_at", null)->update(['is_enabled_invoice' => $sts]);
            }
        }

        if ($category_prefix == "touronkai") {
            $temp = Event::where("category_type", 3)->where("status", 1);
            if ($code) {
                $temp->where('code', $code);
            }
            $tmpdata = $temp->get();
            foreach ($tmpdata as $value) {
                $flg = Attendee::where('event_id', $value->id)->where("deleted_at", null)->update(['is_enabled_invoice' => $sts]);
            }
        }

        if ($category_prefix == "kyosan") {
            $temp = Event::where("category_type", 5)->where("status", 1);
            if ($code) {
                $temp->where('code', $code);
            }
            $tmpdata = $temp->get();
            foreach ($tmpdata as $value) {
                $flg = Attendee::where('event_id', $value->id)->where("deleted_at", null)->update(['is_enabled_invoice' => $sts]);
            }
        }

        if ($category_prefix == "kosyukai") {
            $temp = Event::where("category_type", 4)->where("status", 1);
            if ($code) {
                $temp->where('code', $code);
            }
            $tmpdata = $temp->get();
            foreach ($tmpdata as $value) {
                $flg = Attendee::where('event_id', $value->id)->where("deleted_at", null)->update(['is_enabled_invoice' => $sts]);
            }
        }


        exit();
    }
    //領収書日付変更
    public function recipedateAjax(Request $request)
    {
        $date = "";
        if ($request->recipe_date == 1) $date = date("Y-m-d H:i:s");
        Attendee::where('id', $request->id)->update(['recipe_date' => $date]);
        exit();
    }

    // 状態切替
    public function changeFlag(Request $request)
    {

        header('Content-Type: application/json');
        $postData = $request->all();
        $record = Event::find($postData[ 'chk' ]);
        if($postData[ 'id' ] === 'attendFlag' ) $record->attendFlag = ($postData[ 'val' ] == 'true')?1:0;
        if($postData[ 'id' ] === 'speakerFlag' ) $record->speakerFlag = ($postData[ 'val' ] == 'true')?1:0;
        if($postData[ 'id' ] === 'speakerMenuFlag' ) $record->speakerMenuFlag = ($postData[ 'val' ] == 'true')?1:0;
        
        $record->save();
        echo json_encode(['success' => $postData]);
    }
    //CSVダウンロード
    public function csvdownload(Request $request)
    {

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

        foreach ($query as $key => $value) {
            $data[$value->user->type][] = $value;
        }


        $c = "";
        if ($this->category['prefix'] == "touronkai") {
            $c = "討論会支払状況";
        }
        if ($this->category['prefix'] == "kosyukai") {
            $c = "講習会支払状況";
        }

        

        // 参加料金区分を複数列に分ける
        // イベント名で絞り込みを行ったときのみ
        $sankaryokinkubun = "参加料金区分";

        // 参加料金区分、参加料金
        $joinstitle = $value->event->event_joins->where('join_status', 1)->where('status', 1);

        /*
        $header = ["ID","会員種別","法人会員番号","法人名","所属部署","郵便番号","法人住所","担当者氏名","ふりがな","担当者電話番号","担当者メールアドレス","メールアドレス公開","年会費","請求書","領収書","協賛学会所属の有無","参加イベント","参加者番号","参加料金区分","参加料金","参加状況",$c];
        */
        $header = ["ID", "会員種別", "法人会員番号", "担当者氏名", "ふりがな", "担当者メールアドレス", "法人名", "所属部署", "郵便番号", "法人住所", "担当者電話番号", "メールアドレス公開", "年会費", "請求書", "領収書", "協賛学会所属の有無", "参加イベント", "参加者番号"];
        // $header[] = $sankaryokinkubun;
        for ($i = 0; $i < count($joinstitle); $i++) {
            $header[] = "参加料金区分";
            $header[] = "参加料金";
        }

        $header[] = "参加状況";
        

        if ($this->category['prefix'] == "kyosan") {
            $header[] = "参加者氏名1";
            $header[] = "参加者請求金額1";
            $header[] = "参加者氏名2";
            $header[] = "参加者請求金額2";
            $header[] = "懇親会参加氏名1";
            $header[] = "懇親会参加金額1";
            $header[] = "懇親会参加氏名2";
            $header[] = "懇親会参加金額2";
        }else{
            $header[] = $c;
        }

        /*
        $header2 = ["ID","会員種別","会員番号","所属","部門","郵便番号","所属住所","担当者氏名","ふりがな","担当者電話番号","担当者メールアドレス","メールアドレス公開","年会費","請求書","領収書","協賛学会所属の有無","参加イベント","参加者番号","参加料金区分","参加料金","参加状況",$c];
*/
        $header2 = ["ID", "会員種別", "会員番号", "担当者氏名", "ふりがな", "担当者メールアドレス", "所属", "部門", "郵便番号", "所属住所", "担当者電話番号", "メールアドレス公開", "年会費", "請求書", "領収書", "協賛学会所属の有無", "参加イベント", "参加者番号"];
        //$header2[] = $sankaryokinkubun;
        for ($i = 0; $i < count($joinstitle); $i++) {
            $header2[] = "参加料金区分";
            $header2[] = "参加料金";
        }
        $header2[] = "参加状況";
        

        if ($this->category['prefix'] == "kyosan") {
            $header2[] = "参加者氏名1";
            $header2[] = "参加者請求金額1";
            $header2[] = "参加者氏名2";
            $header2[] = "参加者請求金額2";
            $header2[] = "懇親会参加氏名1";
            $header2[] = "懇親会参加金額1";
            $header2[] = "懇親会参加氏名2";
            $header2[] = "懇親会参加金額2";
        }else{
            $header2[] = $c;
        }

        $users = [];


        $inputs_h = FormInput::where(['form_type' => $this->form['key'], 'is_display_user_list' => 1])->get();


        foreach ($inputs_h as $item_h) {
            if ($this->category['prefix'] == "kosyukai") {
                if ($item_h->csvflag == true) {
                    if ($item_h->csvtag == 0) {
                        array_push($header, $item_h->name);
                        array_push($header2, $item_h->name);
                    } else {
                        array_splice($header, $item_h->csvtag - 1, 0, $item_h->name);
                        array_splice($header2, $item_h->csvtag - 1, 0, $item_h->name);
                    }
                }
            }
        }

        foreach ($array_type as $keys => $values) {
            if (isset($header)) {
                $users[] = [$values];
                if ($keys == 1 || $keys == 4) {
                    $users[] = $header2;
                } else {
                    $users[] = $header;
                }
                if (isset($data[$keys])) {
                    foreach ($data[$keys] as $key => $value) {
                        
                        $num = sprintf('%010d', $value->event_number);
                        $clum = [];

                        $clum[] = $value->user->login_id;
                        $clum[] = $array_type[$value->user->type];

                        if ($value->user->type == 2) $clum[] = $value->user->type_number;
                        if ($value->user->type == 3) $clum[] = $value->user->type_number;
                        if ($value->user->type == 4) $clum[] = $value->user->type_number;
                        if ($value->user->type == 1) $clum[] = "";
                        if ($value->user->type == 5) $clum[] = "";
                        if ($value->user->type == 6) $clum[] = "";



                        if ($value->user->type == 2) $clum[] = $value->user->sei . " " . $value->user->mei;
                        if ($value->user->type == 3) $clum[] = $value->user->sei . " " . $value->user->mei;
                        if ($value->user->type == 4) $clum[] = $value->user->sei . " " . $value->user->mei;
                        if ($value->user->type == 1) $clum[] = $value->user->sei . " " . $value->user->mei;
                        if ($value->user->type == 5) $clum[] = $value->user->sei . " " . $value->user->mei;
                        if ($value->user->type == 6) $clum[] = $value->user->sei . " " . $value->user->mei;


                        if ($value->user->type == 2) $clum[] = $value->user->sei_kana . " " . $value->user->mei_kana;
                        if ($value->user->type == 3) $clum[] = $value->user->sei_kana . " " . $value->user->mei_kana;
                        if ($value->user->type == 4) $clum[] = $value->user->sei_kana . " " . $value->user->mei_kana;
                        if ($value->user->type == 1) $clum[] = $value->user->sei_kana . " " . $value->user->mei_kana;
                        if ($value->user->type == 5) $clum[] = $value->user->sei_kana . " " . $value->user->mei_kana;
                        if ($value->user->type == 6) $clum[] = $value->user->sei_kana . " " . $value->user->mei_kana;


                        if ($value->user->type == 2) $clum[] = $value->user->email;
                        if ($value->user->type == 3) $clum[] = $value->user->email;
                        if ($value->user->type == 4) $clum[] = $value->user->email;
                        if ($value->user->type == 1) $clum[] = $value->user->email;
                        if ($value->user->type == 5) $clum[] = $value->user->email;
                        if ($value->user->type == 6) $clum[] = $value->user->email;







                        if ($value->user->type == 2) $clum[] = $value->user->cp_name;
                        if ($value->user->type == 3) $clum[] = $value->user->cp_name;
                        if ($value->user->type == 4) $clum[] = "";
                        if ($value->user->type == 1) $clum[] = "";
                        if ($value->user->type == 5) $clum[] = $value->user->cp_name;
                        if ($value->user->type == 6) $clum[] = $value->user->cp_name;


                        if ($value->user->type == 2) $clum[] = $value->user->busyo;
                        if ($value->user->type == 3) $clum[] = $value->user->busyo;
                        if ($value->user->type == 4) $clum[] = $value->user->busyo;
                        if ($value->user->type == 1) $clum[] = $value->user->busyo;
                        if ($value->user->type == 5) $clum[] = $value->user->busyo;
                        if ($value->user->type == 6) $clum[] = $value->user->busyo;


                        if ($value->user->type == 2) $clum[] = $value->user->postcode;
                        if ($value->user->type == 3) $clum[] = $value->user->postcode;
                        if ($value->user->type == 4) $clum[] = $value->user->postcode;
                        if ($value->user->type == 1) $clum[] = $value->user->postcode;
                        if ($value->user->type == 5) $clum[] = $value->user->postcode;
                        if ($value->user->type == 6) $clum[] = $value->user->postcode;



                        if ($value->user->type == 2) $clum[] = $value->user->address;
                        if ($value->user->type == 3) $clum[] = $value->user->address;
                        if ($value->user->type == 4) $clum[] = $value->user->address;
                        if ($value->user->type == 1) $clum[] = $value->user->address;
                        if ($value->user->type == 5) $clum[] = $value->user->address;
                        if ($value->user->type == 6) $clum[] = $value->user->address;


                        if ($value->user->type == 2) $clum[] = "'" . $value->user->tel;
                        if ($value->user->type == 3) $clum[] = "'" . $value->user->tel;
                        if ($value->user->type == 4) $clum[] = "'" . $value->user->tel;
                        if ($value->user->type == 1) $clum[] = "'" . $value->user->tel;
                        if ($value->user->type == 5) $clum[] = "'" . $value->user->tel;
                        if ($value->user->type == 6) $clum[] = "'" . $value->user->tel;







                        if ($value->user->type == 2) $clum[] = config('pacd.open_address_flag')[$value->user->open_address_flag];
                        if ($value->user->type == 3) $clum[] = config('pacd.open_address_flag')[$value->user->open_address_flag];
                        if ($value->user->type == 4) $clum[] = config('pacd.open_address_flag')[$value->user->open_address_flag];
                        if ($value->user->type == 5) $clum[] = "";
                        if ($value->user->type == 6) $clum[] = "";
                        if ($value->user->type == 1) $clum[] = "";

                        $clum[] = "";
                        $clum[] = "";
                        $clum[] = "";
                        $clum[] = "";

                        $clum[] = $value->event->name;
                        $clum[] = "=\"" . $num . "\"";


                        // 参加料金区分、参加料金
                        $joins = $value->event->event_joins->where('join_status', 1)->where('status', 1);
                        $explode = explode(",", $value->event_join_id_list);
                        //$clumJN = array();
                        //$clumJP = array();
                        // foreach($joins as $join) {
                        //     if(in_array($join->id,$explode)) $clumJN[] = $join->join_name;
                        //     if(in_array($join->id,$explode)) $clumJP[] = $join->join_price;
                        // }
                        if(count($joins) < 1 && $this->category['prefix'] == "kyosan"){
                            // 協賛企業の場合データが無かった際のスペース埋め
                            for($i = 0 ; $i <=7; $i++){
                                $clum[] = "";
                            }
                        }else{
                            foreach ($joins as $join) {
                                if (in_array($join->id, $explode)) {
                                    $clum[] = $join->join_name;
                                } else {
                                    $clum[] = " ";
                                }
                                if (in_array($join->id, $explode)) {
                                    if($value->discountSelectFlag == 1 && $value->event->discountRate && $join->pattern == 1){
                                        $price = $join->join_price * ((100-$value->event->discountRate)/100);
                                    }else{
                                        $price = $join->join_price;
                                    }
                                    $clum[] = $price;
                                } else {
                                    $clum[] = " ";
                                }
                            }
                        }

                        // $clum[] = implode(' ', $clumJN);
                        // $clum[] = implode(' ', $clumJP);

                        $clum[] = ($value->join_status == 0) ? "-" : "〇";
                        
                        if ($this->category['prefix'] == "touronkai" || $this->category['prefix'] == "kosyukai") {
                            $clum[] = ($value->is_paid == 0) ? "未払い" : "支払済み";
                        }
                        if ($this->category['prefix'] == "kyosan") {
                            $clum[] = $value->tenjiSanka1Name;
                            $clum[] = $value->tenjiSanka1Money;
                            $clum[] = $value->tenjiSanka2Name;
                            $clum[] = $value->tenjiSanka2Money;
                            $clum[] = $value->konsinkaiSanka1Name;
                            $clum[] = $value->konsinkaiSanka1Money;
                            $clum[] = $value->konsinkaiSanka2Name;
                            $clum[] = $value->konsinkaiSanka2Money;
                        }


                        $custom_data = $value->custom_form_data->keyBy('form_input_value_id');
                        $inputs = FormInput::where(['form_type' => $this->form['key'], 'is_display_user_list' => 1])->get();


                        $clumData = array();

                        foreach ($inputs as $item) {
                            $no = 0;
                            foreach ($item->values as $value) {
                                if (isset($custom_data[$value->id])) {
                                    // タイトルに振り読み予定日があるときは月日のを挿入
                                    $unit = "";
                                    if (preg_match("/振込予定日/", $item->name)) {
                                        if ($no === 0) $unit = "月";
                                        if ($no === 1) $unit = "日";
                                    }
                                    $clumData[$value->form_input_id][] = $custom_data[$value->id]->data . $unit;

                                    if (isset($custom_data[$value->id]->data_sub)) {
                                        $clumData[$value->form_input_id][] = $custom_data[$value->id]->data_sub;
                                    }
                                } else {
                                    $clumData[$value->form_input_id][] = '';
                                }
                                $no++;
                            }

                            if (isset($clumData[$value->form_input_id])) {
                                if ($item->csvflag == true) {
                                    if ($item->csvtag == 0) {
                                        $clum[] = implode(' ', $clumData[$value->form_input_id]);
                                    } else {
                                        array_splice($clum, $item->csvtag - 1, 0, implode(' ', $clumData[$value->form_input_id]));
                                    }
                                }
                            }
                        }


                        $users[] = $clum;
                    }
                }
                //  $users[] = [];
            }
        }


        //  $stream = fopen('php://output', 'w');
        $f = fopen('test.csv', 'w');
        foreach ($users as $user) {
            $user = mb_convert_encoding($user, 'SJIS-win', 'UTF-8');
            fputcsv($f, $user);
        }
        $filename = date('YmdHis') . ".csv";

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        );

        fclose($f);
        // HTTPヘッダ
        header("Content-Type: application/octet-stream");
        //   header('Content-Length: '.filesize($filename));
        header('Content-Disposition: attachment; filename=' . $filename);
        readfile("test.csv");


        //return Response::make('', 200, $headers);

        exit();
    }
}
