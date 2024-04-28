<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\FormDataCommon;
use App\Models\FormInput;
use App\Models\FormInputValue;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\Mailforms;
use Illuminate\Support\Facades\Mail;
use App\Models\Payment;
use Illuminate\Support\Facades\Redirect;
use App\Library\CommonEventClass;
use App\Models\Attendee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Yearpayment;
use App\Models\Event;

ini_set('max_execution_time', 0);

class MembersController extends Controller
{
    // 会員一覧ページ表示
    public function index(Request $request)
    {
        $currentPath= Route::getFacadeRoot()->current()->uri();

       // $users = User::paginate(20);
        $users = User::where('deleted_at',null);
        $this->request = $request;
        if($this->request->code){
            $users=$users->where(function($users){
                $users = $users->orwhere('sei','like','%'.$this->request->code.'%');
                $users = $users->orwhere('mei','like','%'.$this->request->code.'%');
                $users = $users->orwhere('sei_kana','like','%'.$this->request->code.'%');
                $users = $users->orwhere('mei_kana','like','%'.$this->request->code.'%');
                $users = $users->orwhere('email','like','%'.$this->request->code.'%');
                $users = $users->orwhere('cp_name','like','%'.$this->request->code.'%');
                $users = $users->orwhere('busyo','like','%'.$this->request->code.'%');
                $users = $users->orwhere('login_id','like','%'.$this->request->code.'%');
                $users = $users->orwhere('type_number','like','%'.$this->request->code.'%');
            });
        }
        $users = $users
            ->orderby("id","desc")
            ->paginate(10);

        $set['inputs'] = FormInput::where(['form_type' => config('pacd.form.type.register.key'), 'is_display_user_list' => 1])->get();

        $params = [];
        $params = ['code'=>$request->code];
        $set['users' ] = $users;
        $set['code'  ] = $request->code;
        $set['params'] = $params;
        $set['currentPath'] = $currentPath;
        return view('admin.members.index', $set);
    }

    //領収書ダウンロード切り替え
    public function recipeStatusAjax($status)
    {
        $update = [
            'is_enabled_invoice' => $status,
        ];
        User::where('deleted_at',null)->update($update);
    }
    // 会員登録ページ表示
    public function create()
    {
        $set['inputs'] = FormInput::where(['form_type' => config('pacd.form.type.register.key')])->get();
        return view('admin.members.create', $set);
    }

    // 会員登録処理（POST）
    public function store(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge(['password' => Hash::make($request->password)]);
            $user = User::create($request->all());
            if ($request->custom) {
                FormDataCommon::createFromInputData($request->custom, $user);
            }
            if ($request->send_mail) {
                // 会員登録メール送信
                $this->mailsend($user->id,$request);
            }
            DB::commit();
            Log::info("【管理者：会員登録】user_id:$user->id");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . '【管理者：会員登録】error:' . $e->getMessage());
            return redirect()->back()->withInput()->with('status', '会員登録に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', '会員を登録しました。');
    }
    //メール配信
    public function mailsend($userid,$request=array()){
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        //ユーザーデータ取得
        $userdata = User::where("id",$userid)->first();

        $this->to = $userdata->email;

        //会員登録用メール取得
        //会員区分事に分ける
        $type = $userdata->type;
        $form_type = "";
        if($type == 1) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.10.key');
        if($type == 2) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.11.key');
        if($type == 3) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.12.key');
        if($type == 4) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.13.key');
        if($type == 5) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.14.key');
        if($type == 6) $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.15.key');
        $mailformat = Mailforms::getData($form_type);


        //タイトルの置き換え
        $title = $mailformat->title ?? '';
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $title = preg_replace("/".$value['replace']."/",$userdata->$key,$title);
        }
        $this->title = $title;
        //本文の置き換え
        $body = $mailformat->note;
        //パスワードの変更
        $body = preg_replace("/##password##/", $_REQUEST['password'],$body);
        foreach(config('pacd.CONST_MAIL_REPLACE.member') as $key=>$value){
            $body = preg_replace("/".$value['replace']."/",$userdata->$key,$body);
        }
        //フォームデータの取得
        $formdata = FormDataCommon::where("user_id",$userid)->get();
        foreach($formdata as $key=>$value){
            $body = preg_replace("/##mem".$value['form_input_value_id']."##/",$value->data,$body);
        }

        $admin = config("admin.email");
        $head = config("admin.head");

        $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
        $header.="\n";
        $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
        $pfrom = "-f$admin";

        if (config("app.env") === "local") {
            //メール配信
            Mail::raw($body, function ($message) {
                $message->to($this->to)
                    ->subject($this->title);
            });
        }else{
            mb_send_mail($this->to , $this->title , $body,$header,$pfrom);
        }
        return true;
    }
    public function addyear($id,Request $request){
        $set = [];
        $set['uid'] = $id;
        $set['years'] = $request->add_year;
        $set['type' ] = 1;
        $set['status' ] = 0;

        $count = Payment::where('uid', $id)
            ->where('years', $request->add_year)
            ->count();
        if($count){
            $request->session()->put('message', "年度追加に失敗しました。");
        }else{
            Payment::insert($set);
            $request->session()->put('message', "年度追加を行いました。");
        }
        return redirect(route('admin.members.payment',$id));
    }
    //支払い状況
    public function payment($id , Request $request){
        Payment::setPayment(1,$id);
        $year = date("Y");
        $set['payments'] = Payment::where(['uid'=>$id])->orderBy('years',"desc")->paginate(20);
        $set['pay' ] = Payment::where(['uid'=>$id,'years'=>$year])->first();
        $set['user'] = User::find($id);
        $set['year'] = date("Y");
        $set['payment'] = config('pacd.payment');
        $set['message'] = $request->session()->get('message',[]);
        $request->session()->forget('message');
        return view('admin.members.payment', $set);
    }
    //資料ダウンロード可否登録
    public function docDownloadUpdateAjax(Request $request){
        $Attendee = Attendee::where(['id'=>$request->id])->first();
        $Attendee->doc_dl = $request->checkbox_list;
        $Attendee->save();
        exit();
    }

    // 資料ダウンロード一括チェック
    public function docAllCheck(Request $request){
        $this->request = $request;
        $search_data = [];
        $code_data = "";
        $category = $request->category;
        //氏名等の検索情報があった場合
        if ($request->search !== null) {
            $query_search = Attendee::select(['attendees.*'
            ,'users.login_id'
            ])
            ->leftJoin('events', 'events.id', '=', 'attendees.event_id')
            ->Join('users', 'attendees.user_id', '=', 'users.id')
            ->where('events.category_type', $category);

            $query_search = $query_search->where(function($query_search){
                $search = preg_replace("/ /","",$this->request->search);
                $query_search = $query_search->orwhere('users.cp_name','like', '%'.$this->request->search.'%');
                $query_search = $query_search->orwhere(DB::raw('CONCAT(users.sei,users.mei)'),'like', '%'.$search.'%');
                $query_search = $query_search->orwhere('users.login_id','like', '%'.$search.'%');
                $query_search = $query_search->orwhere('users.email','like', '%'.$search.'%');
                if (preg_match("/^[a-zA-Z0-9]+$/", $search)) {
                    $query_search = $query_search->orwhere('attendees.event_number','like', '%'.(int)$search.'%');
                }
                $query_search = $query_search->where('users.deleted_at',null);
            });

            $searches = $query_search->get();
            foreach($searches as $search){
                $search_data[] = ($search->user_id);
            };
        }

        // イベントの指定があった場合
        if ($request->code !== "0" || $request->code!==null) {
            $query_code = Event::select('id')
                ->where('code', $request->code)
                ->where('category_type', $category)
                ->where('status', 1);

            $codes = $query_code->get();
            foreach($codes as $code){
                $code_data = ($code->id);
            };
        }

        //検索＆イベント指定の場合
        if ($request->search!==null && $request->code!=="0" && $request->code!==null) {
            $Attendee = Attendee::where('event_id', $code_data)
                ->whereIn('user_id', $search_data);

        //イベントのみ指定された場合
        } elseif ($request->search===null && $request->code!=="0" && $request->code!==null) {
            $Attendee = Attendee::Join('users', 'attendees.user_id', '=', 'users.id')
                ->where('attendees.event_id', $code_data)
                ->where('users.deleted_at', null);
        //検索だけされた場合
        } elseif ($request->search!==null && ($request->code==="0" || $request->code===null)) {
            $Attendee = Attendee::leftJoin('events', 'events.id', '=', 'attendees.event_id')
                ->where('events.status', 1)
                ->where('attendees.user_id', $search_data);
        //どっちもされてない場合
        } else {
            $Attendee = Attendee::leftJoin('events', 'events.id', '=', 'attendees.event_id')
                ->Join('users', 'attendees.user_id', '=', 'users.id')
                ->where('events.category_type', $category)
                ->where('users.deleted_at',null)
                ->where('events.status', 1);
        }

        $doc_dl = $request->checked === 'true' ? 1 : 0;
        $Attendee->update(['doc_dl' => $doc_dl]);

        exit();
    }
    //参加者支払い状況登録
    public function paymentupdateAtendeeAjax(Request $request){
        $Attendee = Attendee::where(['id'=>$request->id])->first();
        $Attendee->is_paid = $request->payment_status;
        $Attendee->save();
        exit();
    }
    // 領収書支払い状況登録
    public function recipeStatusUpdateAjax(Request $request){
        $Attendee = Attendee::where(['id'=>$request->id])->first();
        $Attendee->recipe_status = $request->recipe_status;
        $Attendee->save();
        exit();
    }
    // 領収書支払い状況登録
    public function joinStatusUpdateAjax(Request $request){
        $Attendee = Attendee::where(['id'=>$request->id])->first();
        $Attendee->join_status = $request->join_status;
        $Attendee->save();
        exit();
    }
    //領収書ダウンロード
    public function invoiceDownloadAjax(Request $request){
        $user = User::where(['id'=>$request->id])->first();
        $user->is_enabled_invoice = $request->is_enabled_invoice;
        $user->save();
        exit();
    }
    public function invoiceJoinDownloadAjax(Request $request){
        $Attendee = Attendee::where(['id'=>$request->id])->first();
        $Attendee->is_enabled_invoice = $request->is_enabled_invoice;
        $Attendee->save();
        exit();
    }
    //支払い状況登録
    //領収書状態変更
    public function paymentupdateAjax($id,Request $request){
        $year = date("Y");
        $payment = Payment::where(['uid'=>$id,'years'=>$year])->first();
        if(empty($payment)){
            $set = [];
            $set['uid'] = $id;
            $set['type'] = config('pacd.category.members.key');
            $set['years'] = $year;
            if(isset($request->recipe_status)){
                $set['recipe_status']=$request->recipe_status;
            }else{
                $set['status']=$request->payment_status;
            }
            Payment::insert($set);
        }else{
            //支払状況変更の際に追加した20211120
            $payment = Payment::where(['id'=>$request->id])->first();
            if(isset($request->recipe_status)){
                $payment->recipe_status = $request->recipe_status;
            }else{
                $payment->status = $request->payment_status;
            }
            $payment->save();
        }

    }


    // 会員情報編集ページ表示
    public function edit($id)
    {
        $set['user'] = User::find($id);
        $set['inputs'] = FormInput::where(['form_type' => config('pacd.form.type.register.key')])->get();
        $set['attendees'] = Attendee::where([ 'user_id'=>$id ])->get();

        return view('admin.members.edit', $set);
    }

    // 会員情報更新処理（PUT/PATCH）
    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            if ($user = User::find($id)) {

                $user->fill($request->all())->save();
                if ($request->custom) {
                    FormDataCommon::updateFormInputData($request->custom, $user);
                }
            }
            DB::commit();
            Log::info("【管理者：会員情報編集】user_id:$id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . '【管理者：会員情報編集】user_id:'. $user->id .', error:' . $e->getMessage());
            return redirect()->back()->withInput()->with('status', '会員情報の更新に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', '会員情報を更新しました。');
    }

    // 会員パスワード変更ページ表示
    public function edit_password($id)
    {
        $set['user'] = User::find($id);
        return view('admin.members.edit_password', $set);
    }

    // 会員パスワード更新処理（PUT/PATCH）
    public function update_password(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|alpha-num-check|min:4|max:16|confirmed',
            'send_mail' => 'nullable|boolean',
        ]);

        try {
            User::find($id)->update(['password' => Hash::make($request->password)]);
            Log::info("【管理者：会員パスワード変更】user_id:$id");
        } catch (\Exception $e) {
            Log::error(Route::currentRouteAction() . '【管理者：会員パスワード変更】user_id:'. $id .', error:' . $e->getMessage());
            return redirect()->back()->withInput()->with('status', '会員パスワードの変更に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', '会員パスワードを変更しました。');
    }

    // 会員削除処理（DELETE）
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            User::find($id)->delete();
            DB::commit();
            Log::info("【管理者：会員削除】user_id: $id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【管理者：会員削除】user_id: $id, error: $e->getMessage()");
            return redirect()->back()->with('flash.error', '会員の削除に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', '会員を削除しました。');
    }

    //CSV出力
    public function csv(){
        //法人会員（窓口担当者）
        $user1 = User::where(['type'=>2])->orderBy('type',"asc")->get();
        $users1 = [];
        $i = 0;
        foreach($user1 as $key=>$value){
            $users1[$i]['login_id'] = $value['login_id'];
            $users1[$i]['type'       ] = config('pacd.user.type')[$value['type']];
            $users1[$i]['type_number'] = $value['type_number'];
            $users1[$i]['cp_name'    ] = $value['cp_name'    ];
            $users1[$i]['address'    ] = $value['address'    ];
            $users1[$i]['name'       ] = $value['sei']." ".$value['mei'];
            $users1[$i]['kana'       ] = $value['sei_kana']." ".$value['mei_kana'];
            $users1[$i]['busyo'      ] = $value['busyo'];
            $users1[$i]['tel'        ] = $value['tel'  ];
            $users1[$i]['email'      ] = $value['email'];
            $i++;
        }

        $count = (isset($users1[0]))?count($users1[0])-2:0;

        //法人会員
        $user2 = User::where(['type'=>3])->orderBy('type',"asc")->get();
        $users2 = [];
        $i = 0;
        foreach($user2 as $key=>$value){
            $users2[$i]['login_id'] = $value['login_id'];
            $users2[$i]['type'       ] = config('pacd.user.type')[$value['type']];
            for($k=0;$k<$count;$k++){$users2[$i][$k] = "";}
            $users2[$i]['type_number'] = $value['type_number'];
            $users2[$i]['cp_name'    ] = $value['cp_name'    ];
            $users2[$i]['address'    ] = $value['address'    ];
            $users2[$i]['name'       ] = $value['sei']." ".$value['mei'];
            $users2[$i]['kana'       ] = $value['sei_kana']." ".$value['mei_kana'];
            $users2[$i]['busyo'      ] = $value['busyo'];
            $users2[$i]['tel'        ] = $value['tel'  ];
            $users2[$i]['email'      ] = $value['email'];
            $i++;
        }

        $count = (isset($users2[0]))?count($users2[0])-2:0;

        //個人会員データ取得
        $user3 = User::where(['type'=>4])->orderBy('type',"asc")->get();
        $users3 = [];
        $i = 0;
        foreach($user3 as $key=>$value){
            $users3[$i]['login_id'   ] = $value['login_id'];
            $users3[$i]['type'       ] = config('pacd.user.type')[$value['type']];
            for($k=0;$k<$count;$k++){$users3[$i][$k] = "";}
            $users3[$i]['type_number'] = $value['type_number'];
            $users3[$i]['name'       ] = $value['sei']." ".$value['mei'];
            $users3[$i]['kana'       ] = $value['sei_kana']." ".$value['mei_kana'];
            $users3[$i]['busyo'      ] = $value['busyo'];
            $users3[$i]['address'    ] = $value['address'];
            $users3[$i]['tel'        ] = $value['tel'  ];
            $users3[$i]['email'      ] = $value['email'];
            $i++;
        }

        $count = (isset($users3[0]))?count($users3[0])-2:0;

        if(empty($users3[0])){
            $count = 0;
        }else{
            $count = count($users3[0])-2;
        }
        //会員外データ取得
        $user4 = User::where(['type'=>1])->orderBy('type',"asc")->get();
        $users4 = [];
        $i = 0;
        foreach($user4 as $key=>$value){
            $users4[$i]['login_id'   ] = $value['login_id'];
            $users4[$i]['type'       ] = config('pacd.user.type')[$value['type']];
            for($k=0;$k<$count;$k++){$users4[$i][$k] = "";}
            $users4[$i]['group_flag' ] = (config('pacd.user.group_flag')[$value['group_flag']])??"";
            $users4[$i]['name'       ] = $value['sei']." ".$value['mei'];
            $users4[$i]['kana'       ] = $value['sei_kana']." ".$value['mei_kana'];
            $users4[$i]['busyo'      ] = $value['busyo'];
            $users4[$i]['address'    ] = $value['address'];
            $users4[$i]['tel'        ] = $value['tel'  ];
            $users4[$i]['email'      ] = $value['email'];
            $i++;
        }
        $count = (isset($users4[0]))?count($users4[0])-2:0;
        //協賛企業データ取得
        $user5 = User::where(['type'=>5])->orderBy('type',"asc")->get();
        $users5 = [];
        $i = 0;
        foreach($user5 as $key=>$value){
            $users5[$i]['login_id'   ] = $value['login_id'];
            $users5[$i]['type'       ] = config('pacd.user.type')[$value['type']];
            for($k=0;$k<$count;$k++){$users5[$i][$k] = "";}
            $users5[$i]['cp_name'    ] = $value['cp_name'];
            $users5[$i]['address'    ] = $value['address'];
            $users5[$i]['name'       ] = $value['sei']." ".$value['mei'];
            $users5[$i]['kana'       ] = $value['sei_kana']." ".$value['mei_kana'];
            $users5[$i]['busyo'      ] = $value['busyo'];
            $users5[$i]['tel'        ] = $value['tel'  ];
            $users5[$i]['email'      ] = $value['email'];
            $i++;
        }

        $count = (isset($users5[0]))?count($users5[0])-2:0;

        //協賛企業枠参加者データ取得
        $user6 = User::where(['type'=>6])->orderBy('type',"asc")->get();
        $users6 = [];
        $i = 0;
        foreach($user6 as $key=>$value){
            $users6[$i]['login_id'   ] = $value['login_id'];
            $users6[$i]['type'       ] = config('pacd.user.type')[$value['type']];
            for($k=0;$k<$count;$k++){$users6[$i][$k] = "";}
            $users6[$i]['cp_name'    ] = $value['cp_name'];
            $users6[$i]['name'       ] = $value['sei']." ".$value['mei'];
            $users6[$i]['kana'       ] = $value['sei_kana']." ".$value['mei_kana'];
            $users6[$i]['busyo'      ] = $value['busyo'];
            $users6[$i]['address'    ] = $value['address'];
            $users6[$i]['tel'        ] = $value['tel'  ];
            $users6[$i]['email'      ] = $value['email'];
            $i++;
        }


        // カラムの作成
        $head1 = [
            'ID',
            '会員種別',
            '法人会員番号',
            '法人名',
            '法人住所',
            '担当者氏名',
            '担当者氏名かな',
            '担当者所属部署',
            '担当者電話番号',
            '担当者メールアドレス',
        ];
        $head2 = [
            '法人会員番号',
            '法人名',
            '法人住所',
            '担当者氏名',
            '担当者氏名かな',
            '担当者所属部署',
            '担当者電話番号',
            '担当者メールアドレス',
            '年会費',
            '請求書',
            '領収書',
        ];
        $head3 = [
            '個人会員番号',
            '氏名',
            'ふりがな',
            '所属',
            '所属住所',
            '電話番号',
            'メールアドレス',
        ];
        $head4 = [
            '協賛学会所属の有無',
            '氏名',
            'ふりがな',
            '所属',
            '所属住所',
            '電話番号',
            'メールアドレス',
        ];
        $head5 = [
            '法人名',
            '法人住所',
            '担当者氏名',
            '担当者氏名かな',
            '担当者所属部署',
            '担当者電話番号',
            'メールアドレス',

        ];
        $head6 = [
            '法人名',
            '氏名',
            'ふりがな',
            '所属部署',
            '住所',
            '電話番号',
            'メールアドレス',
        ];



        $userlist = array_merge($users1,$users2);
        $userlist = array_merge($userlist,$users3);
        $userlist = array_merge($userlist,$users4);
        $userlist = array_merge($userlist,$users5);
        $userlist = array_merge($userlist,$users6);


        $head = array_merge($head1,$head2);
        $head = array_merge($head,$head3);
        $head = array_merge($head,$head4);
        $head = array_merge($head,$head5);
        $head = array_merge($head,$head6);
        CommonEventClass::createCsv($userlist,$head);

        exit();
    }
    //csv
    public function csv_parts($id=""){

        $sql = "
        select
            users.*
            ,count(users.id) as count
            ,SUM(CASE WHEN payments.status=0 THEN 1 ELSE 0 END) as recipe_paid
            ,max(payments.years) as payment_year
            ,(SELECT status from payments where uid=users.id AND years = (SELECT max(payments.years) FROM payments WHERE uid=users.id)) as paystatus
            ,(SELECT invoice_status from payments where uid=users.id AND years = (SELECT max(payments.years) FROM payments WHERE uid=users.id)) as invoice_3year
            ,(SELECT recipe_status from payments where uid=users.id AND years = (SELECT max(payments.years) FROM payments WHERE uid=users.id)) as recipe_3year
        from
            `users` left join payments on users.id = payments.uid AND payments.years >= ".$this->getYearfor2Last()."
        where (users.type = :id) and
            users.deleted_at is null
            group by users.id order by users.type asc, users.id
        ";

        $user = DB::select($sql, ['id' => $id]);

        //会員外
        if($id == 1) $this->outputCsv1($user);
        //法人会員(窓口担当者)
        if($id == 2) $this->outputCsv2($user);
        //法人会員
        if($id == 3) $this->outputCsv3($user);
        //個人会員
        if($id == 4) $this->outputCsv4($user);
        //協賛企業
        if($id == 5) $this->outputCsv5($user);
        //協賛企業枠参加者
        if($id == 6) $this->outputCsv6($user);


        exit();
    }

    public function outputCsv1($user){
        $users = [];
        $i = 0;
        foreach($user as $key=>$value){
            $users[$i]['login_id'   ] = $value->login_id;
            $users[$i]['type'       ] = config('pacd.user.type')[$value->type];
            $users[$i]['number'     ] = "";
            $users[$i]['busyo'      ] = $value->busyo;
            $users[$i]['bumon'      ] = $value->bumon;
            $users[$i]['postcode'   ] = $value->postcode;
            $users[$i]['address'    ] = $value->address;
            $users[$i]['name'       ] = $value->sei." ".$value->mei;
            $users[$i]['kana'       ] = $value->sei_kana." ".$value->mei_kana;
            $users[$i]['tel'        ] = '="'.$value->tel.'"';
            $users[$i]['email'      ] = $value->email;
            $users[$i]['blank1'     ] = ''; //メールアドレス公開

/*
            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.1');
            $users[$i]['invoice'] = config('pacd.payment')[$value->paystatus];
            $users[$i]['recipe' ] = $value->payment_year."年";
            $users[$i]['group_flag' ] = (config('pacd.user.group_flag')[$value->group_flag])??"";
*/
            if($value->payment_year != $this->getYearfor()){
                //今年度のデータがないときは0(未払い)を強制
                $value->paystatus = 0;
                //未払い分の計算について+1を強制
                $value->recipe_paid = $value->recipe_paid+1;
            }
            $users[$i]['years'] = $this->getYearfor();
            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.3');
            //$users[$i]['paystatus'] = $this->getPayStatus($value->paystatus*config('pacd.user.yearPrice.1'));
            $users[$i]['paystatus'] = $this->getPayStatus($value->paystatus);
            $users[$i]['recipe'] = $this->getRecipeStatus($value->recipe_3year,$value->paystatus);
            $users[$i]['invoice'] = $this->getInvoiceStatus($value->invoice_3year);
            $users[$i]['group_flag' ] = (config('pacd.user.group_flag')[$value->group_flag])??"";

            $i++;
        }
        $head = [
            "ID",
            "会員種別",
            "会員番号",
            "所属",
            "部門",
            "郵便番号",
            "所属住所",
            "氏名",
            "ふりがな",
            "電話番号",
            "メールアドレス",
            "メールアドレス公開",
            '年度',
            '年会費',
            '支払い状況',
            '領収書',
            '請求書',
            '協賛学会所属の有無',

        ];
        CommonEventClass::createCsv($users,$head);
    }
    public function outputCsv2($user){
        $users = [];
        $i = 0;
        foreach($user as $key=>$value){
            $users[$i]['login_id'   ] = $value->login_id;
            $users[$i]['type'       ] = config('pacd.user.type')[$value->type];
            $users[$i]['type_number'] = $value->type_number;
            $users[$i]['cp_name'    ] = $value->cp_name;
            $users[$i]['busyo'      ] = $value->busyo;
            $users[$i]['postcode'   ] = $value->postcode;
            $users[$i]['address'    ] = $value->address;
            $users[$i]['name'       ] = $value->sei." ".$value->mei;
            $users[$i]['kana'       ] = $value->sei_kana." ".$value->mei_kana;
            $users[$i]['tel'        ] = '="'.$value->tel.'"';
            $users[$i]['email'      ] = $value->email;
            $users[$i]['open_address_flag'] = config('pacd.open_address_flag')[$value->open_address_flag];

            if($value->payment_year != $this->getYearfor()){
                //今年度のデータがないときは0(未払い)を強制
                $value->paystatus = 0;
                //未払い分の計算について+1を強制
                $value->recipe_paid = $value->recipe_paid+1;
            }

            $users[$i]['years'] = $this->getYearfor();
            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.2');
            //$users[$i]['paystatus'] = $this->getPayStatus($value->recipe_paid*config('pacd.user.yearPrice.2'));
            $users[$i]['paystatus'] = $this->getPayStatus($value->paystatus);
            $users[$i]['recipe'] = $this->getRecipeStatus($value->recipe_3year,$value->paystatus);
            $users[$i]['invoice'] = $this->getInvoiceStatus($value->invoice_3year);
            $users[$i]['blank1'] = "";
            /*
            //未払い分合算
            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.2');
            //最新年の支払い
            $users[$i]['invoice'] = config('pacd.payment')[$value->paystatus];
            //最新年
            $users[$i]['recipe'] = $value->payment_year."年";
            $users[$i]['blank1'] = "";
            */

            $i++;
        }
        $head = [
            'ID',
            '会員種別',
            '法人会員番号',
            '法人名',
            '所属部署',
            '郵便番号',
            '法人住所',
            '担当者氏名',
            'ふりがな',
            '担当者電話番号',
            '担当者メールアドレス',
            'メールアドレス公開',
            '年度',
            '年会費',
            '支払い状況',
            '領収書',
            '請求書',
            '協賛学会所属の有無',

        ];
        CommonEventClass::createCsv($users,$head);
    }
    public function outputCsv3($user){
        $users = [];
        $i = 0;
        foreach($user as $key=>$value){
            $users[$i]['login_id'] = $value->login_id;
            $users[$i]['type'       ] = config('pacd.user.type')[$value->type];
            $users[$i]['type_number'] = $value->type_number;
            $users[$i]['cp_name'    ] = $value->cp_name;
            $users[$i]['busyo'      ] = $value->busyo;
            $users[$i]['postcode'   ] = $value->postcode;
            $users[$i]['address'    ] = $value->address;
            $users[$i]['name'       ] = $value->sei." ".$value->mei;
            $users[$i]['kana'       ] = $value->sei_kana." ".$value->mei_kana;
            $users[$i]['tel'        ] = '="'.$value->tel.'"';
            $users[$i]['email'      ] = $value->email;
            $users[$i]['open_address_flag'] = config('pacd.open_address_flag')[$value->open_address_flag];
            $users[$i]['years'] = $this->getYearfor();
            if($value->payment_year != $this->getYearfor()){
                //今年度のデータがないときは0(未払い)を強制
                $value->paystatus = 0;
                //未払い分の計算について+1を強制
                $value->recipe_paid = $value->recipe_paid+1;
            }

            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.3');
           // $users[$i]['paystatus'] = $this->getPayStatus($value->recipe_paid*config('pacd.user.yearPrice.3'));
            $users[$i]['paystatus'] = $this->getPayStatus($value->paystatus);
            $users[$i]['recipe'] = $this->getRecipeStatus($value->recipe_3year,$value->paystatus);
            $users[$i]['invoice'] = $this->getInvoiceStatus($value->invoice_3year);
            $users[$i]['blank1'] = "";

            $i++;
        }
        $head = [
            'ID',
            '会員種別',
            '法人会員番号',
            '法人名',
            '所属部署',
            '郵便番号',
            '法人住所',
            '担当者氏名',
            'ふりがな',
            '担当者電話番号',
            '担当者メールアドレス',
            'メールアドレス公開',
            '年度',
            '年会費',
            '支払い状況',
            '領収書',
            '請求書',
            '協賛学会所属の有無',
        ];
        CommonEventClass::createCsv($users,$head);
    }
    public function outputCsv4($user){
        $users = [];
        $i = 0;
        foreach($user as $key=>$value){
            $users[$i]['login_id'   ] = $value->login_id;
            $users[$i]['type'       ] = config('pacd.user.type')[$value->type];
            $users[$i]['type_number'] = $value->type_number;
            $users[$i]['busyo'      ] = $value->busyo;
            $users[$i]['bumon'      ] = $value->bumon;
            $users[$i]['postcode'   ] = $value->postcode;
            $users[$i]['address'    ] = $value->address;
            $users[$i]['name'       ] = $value->sei." ".$value->mei;
            $users[$i]['kana'       ] = $value->sei_kana." ".$value->mei_kana;
            $users[$i]['tel'        ] = '="'.$value->tel.'"';
            $users[$i]['email'      ] = $value->email;
            $users[$i]['open_address_flag'      ] = config('pacd.open_address_flag')[$value->open_address_flag];
            //未払い分合算
            //$users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.4');
            //最新年の支払い
            //$users[$i]['invoice'] = config('pacd.payment')[$value->paystatus];
            //最新年
            //$users[$i]['recipe'] = $value->payment_year."年";
            //$users[$i]['blank1'] = "";
            $users[$i]['years'] = $this->getYearfor();
            if($value->payment_year != $this->getYearfor()){
                //今年度のデータがないときは0(未払い)を強制
                $value->paystatus = 0;
                //未払い分の計算について+1を強制
                $value->recipe_paid = $value->recipe_paid+1;
            }
            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.4');
            //$users[$i]['paystatus'] = $this->getPayStatus($value->recipe_paid*config('pacd.user.yearPrice.4'));
            $users[$i]['paystatus'] = $this->getPayStatus($value->paystatus);

            $users[$i]['recipe'] = $this->getRecipeStatus($value->recipe_3year,$value->paystatus);
            $users[$i]['invoice'] = $this->getInvoiceStatus($value->invoice_3year);
            $users[$i]['blank1'] = "";
            $i++;
        }
        $head = [
            "ID",
            "会員種別",
            "個人会員番号",
            "所属",
            "部門",
            "郵便番号",
            "所属住所",
            "氏名",
            "ふりがな",
            "電話番号",
            "メールアドレス",
            "メールアドレス公開",
            //'年会費',
            //'請求書',
            //'領収書',
            //'協賛学会所属の有無',
            '年度',
            '年会費',
            '支払い状況',
            '領収書',
            '請求書',
            '協賛学会所属の有無',
        ];
        CommonEventClass::createCsv($users,$head);
    }
    public function outputCsv5($user){
        $users = [];
        $i = 0;
        foreach($user as $key=>$value){
            $users[$i]['login_id'   ] = $value->login_id;
            $users[$i]['type'       ] = config('pacd.user.type')[$value->type];
            $users[$i]['blank1'     ] = "";
            $users[$i]['cp_name'    ] = $value->cp_name;
            $users[$i]['busyo'      ] = $value->busyo;
            $users[$i]['blank2'     ] = "";
            $users[$i]['address'    ] = $value->address;
            $users[$i]['name'       ] = $value->sei." ".$value->mei;
            $users[$i]['kana'       ] = $value->sei_kana." ".$value->mei_kana;
            $users[$i]['tel'        ] = '="'.$value->tel.'"';
            $users[$i]['email'      ] = $value->email;
            $users[$i]['blank3'     ] = "";

            $users[$i]['years'] = $this->getYearfor();
            if($value->payment_year != $this->getYearfor()){
                //今年度のデータがないときは0(未払い)を強制
                $value->paystatus = 0;
                //未払い分の計算について+1を強制
                $value->recipe_paid = $value->recipe_paid+1;
            }

            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.5');
            //$users[$i]['paystatus'] = $this->getPayStatus($value->recipe_paid*config('pacd.user.yearPrice.5'));
            $users[$i]['paystatus'] = $this->getPayStatus($value->paystatus);

            $users[$i]['recipe'] = $this->getRecipeStatus($value->recipe_3year,$value->paystatus);
            $users[$i]['invoice'] = $this->getInvoiceStatus($value->invoice_3year);
            $users[$i]['blank7'     ] = "";

            //$users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.1');
            //$users[$i]['invoice'] = config('pacd.payment')[$value->paystatus];
            //$users[$i]['recipe' ] = $value->payment_year."年";
            //$users[$i]['blank7'     ] = "";
            $i++;
        }
        $head = [
            'ID',
            '会員種別',
            '会員番号',
            '法人名',
            '所属部署',
            '郵便番号',
            '法人住所',
            '担当者氏名',
            'ふりがな',
            '担当者電話番号',
            'メールアドレス',
            'メールアドレス公開',
            //'年会費',
            //'領収書',
            //'請求書',
            //'協賛学会所属の有無',
            '年度',
            '年会費',
            '支払い状況',
            '領収書',
            '請求書',
            '協賛学会所属の有無',

        ];
        CommonEventClass::createCsv($users,$head);
    }
    public function outputCsv6($user){
        $users = [];
        $i = 0;
        foreach($user as $key=>$value){
            $users[$i]['login_id'   ] = $value->login_id;
            $users[$i]['type'       ] = config('pacd.user.type')[$value->type];
            $users[$i]['blank1'     ] = "";
            $users[$i]['cp_name'    ] = $value->cp_name;
            $users[$i]['busyo'      ] = $value->busyo;
            $users[$i]['blank2'     ] = "";
            $users[$i]['address'    ] = $value->address;
            $users[$i]['name'       ] = $value->sei." ".$value->mei;
            $users[$i]['kana'       ] = $value->sei_kana." ".$value->mei_kana;
            $users[$i]['tel'        ] = '="'.$value->tel.'"';
            $users[$i]['email'      ] = $value->email;
            $users[$i]['blank3'     ] = "";
            //$users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.1');
            //$users[$i]['invoice'] = config('pacd.payment')[$value->paystatus];
            //$users[$i]['recipe' ] = $value->payment_year."年";
            //$users[$i]['blank7'     ] = "";

            $users[$i]['years'] = $this->getYearfor();

            if($value->payment_year != $this->getYearfor()){
                //今年度のデータがないときは0(未払い)を強制
                $value->paystatus = 0;
                //未払い分の計算について+1を強制
                $value->recipe_paid = $value->recipe_paid+1;
            }
            $users[$i]['yearpay'] = $value->recipe_paid*config('pacd.user.yearPrice.6');
            //$users[$i]['paystatus'] = $this->getPayStatus($value->recipe_paid*config('pacd.user.yearPrice.6'));
            $users[$i]['paystatus'] = $this->getPayStatus($value->paystatus);

            $users[$i]['recipe'] = $this->getRecipeStatus($value->recipe_3year,$value->paystatus);
            $users[$i]['invoice'] = $this->getInvoiceStatus($value->invoice_3year);
            $users[$i]['blank7'     ] = "";
            $i++;
        }
        $head = [
            'ID',
            '会員種別',
            '会員番号',
            '法人名',
            '所属部署',
            '郵便番号',
            '住所',
            '氏名',
            'ふりがな',
            '電話番号',
            'メールアドレス',
            'メールアドレス公開',
            //'年会費',
            //'領収書',
            //'請求書',
            //'協賛学会所属の有無',

            '年度',
            '年会費',
            '支払い状況',
            '領収書',
            '請求書',
            '協賛学会所属の有無',
        ];
        CommonEventClass::createCsv($users,$head);
    }

    public function getYearfor($start_date='02/01'){
        $today = date('Y/m/d');
        $start_year = date('Y').'/'.$start_date;// 2015/04/01 or 2016/04/01
        if(strtotime($today) >= strtotime($start_year)){
          $year = date('Y');
        }else{
          $year = date('Y') - 1;
        }
        return (int)$year;
    }

    public function getYearfor2Last($start_date='02/01'){
        $today = date('Y/m/d');
        $start_year = date('Y').'/'.$start_date;// 2015/04/01 or 2016/04/01
        if(strtotime($today) >= strtotime($start_year)){
          $year = date('Y',strtotime("-2 year"));
        }else{
          $year = date('Y',strtotime("-2 year")) - 1;
        }
        return (int)$year;
    }

    public function getInvoiceStatus($status){
        if($status == 1){
            return "発行済み";
        }else{
            return "未発行";
        }
    }

    public function getRecipeStatus($recipe, $status){
        if($recipe == 1 && $status == 1){
            return "発行済み";
        }else{
            return "未発行";
        }
    }

    public function getPayStatus($pay){
        if($pay > 0){
            $return = "支払済み";
        }else{
            $return = "未払い";
        }
        return $return;
    }


    //会員一覧アップロード
    public function upload(Request $request){
        set_time_limit(0);
        $set = [];
        $set['user'] = Auth::user();
        $user_id = Auth::id();
        $file = $request->file('csv_file');

        if($request->upload === "on"){
            if(empty($file)){
                $request->session()->put('message', "ファイルが選択されていません。");
                return redirect(route('admin.members.upload'));
            }else{
                // アップロードファイルに対してのバリデート
                $validator = $this->validateUploadFile($request);

                if ($validator->fails() === true){
                    $request->session()->put('message', "アップロードに失敗しました。");
                    return redirect(route('admin.members.upload'));
                }

                //添付ファイルのアップロード
                $temp_file = "";
                $temp_file_name = "";
                if($request->file('temp_file')){
                    $ext = $request->file('temp_file')->getClientOriginalExtension();
                    $temp_file = $request->file('temp_file')->store('temp');
                    $temp_file_name = "file-".date("Ymdhis").".{$ext}";
                    $fp = fopen(storage_path('app/') . $temp_file, 'r');
                    fclose($fp);
                }

                $temporary_csv_file = $request->file('csv_file')->store('csv');
                $fp = fopen(storage_path('app/') . $temporary_csv_file, 'r');
                $i = 1;
                //会員タイプ
                $array_type = config('pacd.user.type');
                $array_flip_type = array_flip($array_type);
                //メール配信
                $form_type = config('pacd.CONST_MAIL_FORM_TEMP.members.16.key');
                $mailformat = Mailforms::getData($form_type);

                $this->title = $mailformat->title;

                $replace = config('pacd.CONST_MAIL_REPLACE.member');

                while ($row = fgetcsv($fp)) {
                    if( $i > 1 ){
                        $email = $row[10];
                        $password = Hash::make($row[3]);
                        $user = [];
                        if($row[4] && $row[0]){ //type_number
                            $user = User::where([
                                'type_number'=>$row[4],
                                'id'=>$row[0],
                            ])->first();
                            $type = mb_convert_encoding($row[1],"UTF-8","SJIS");
                            if(isset($user->id) && $user->id > 0){
                                $update = [];
                                $update['login_id'] = $row[2];
                                $update['type'    ] = $array_flip_type[$type];
                                $update['sei'     ] = mb_convert_encoding($row[5],"UTF-8","SJIS");
                                $update['mei'     ] = mb_convert_encoding($row[6],"UTF-8","SJIS");
                                $update['sei_kana'] = mb_convert_encoding($row[7],"UTF-8","SJIS");
                                $update['mei_kana'] = mb_convert_encoding($row[8],"UTF-8","SJIS");
                                $update['tel'     ] = $row[9];
                                $update['email'   ] = $row[10];
                                $update['busyo'   ] = mb_convert_encoding($row[11],"UTF-8","SJIS");
                                $update['address' ] = mb_convert_encoding($row[12],"UTF-8","SJIS");
                                if($row[3]){
                                    $update['password'] = $password;
                                }
                                User::where('id',$user->id)
                                    ->update($update);

                            /*
                            else{

                                User::insert([


                                ]);
                            }
                            */


                                mb_language("Japanese");
                                mb_internal_encoding("UTF-8");

                                $admin = config("admin.email");
                                $head = config("admin.head");


                                $file = "";
                                $filename = "";
                                //添付ファイルがある場合は添付ファイル優先
                                if($temp_file){
                                    $file = storage_path("app/")."{$temp_file}";
                                    $filename = $temp_file_name;
                                }




                                $body = $mailformat->note;
                                $body = preg_replace("/".$replace['login_id']['replace']."/",$row[2],$body);
                                $body = preg_replace("/".$replace['sei']['replace']."/",mb_convert_encoding($row[5],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['mei']['replace']."/",mb_convert_encoding($row[6],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['sei_kana']['replace']."/",mb_convert_encoding($row[7],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['mei_kana']['replace']."/",mb_convert_encoding($row[8],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['tel']['replace']."/",mb_convert_encoding($row[9],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['email']['replace']."/",mb_convert_encoding($row[10],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['busyo']['replace']."/",mb_convert_encoding($row[11],"UTF-8","SJIS"),$body);
                                $body = preg_replace("/".$replace['address']['replace']."/",mb_convert_encoding($row[12],"UTF-8","SJIS"),$body);

                                if($row[3]){
                                    $body = preg_replace("/".$replace['passwords']['replace']."/",mb_convert_encoding($row[3],"UTF-8","SJIS"),$body);
                                }else{
                                    $body = preg_replace("/".$replace['passwords']['replace']."/","変更なし",$body);
                                }

                                Log::debug('会員一括登録');
                                Log::debug(date('Y/m/d H:i:s'));
                                Log::debug($this->title);
                                Log::debug($body);


                                $header = '';
                                $text = "";
                                if($file){
                                    // テキストメッセージを記述
                                    $text = "--__BOUNDARY__\n";
                                    $text .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
                                    $text .= $body . "\n";
                                    $text .= "--__BOUNDARY__\n";

                                    // ファイルを添付
                                    $text .= "Content-Type: application/octet-stream; name=\"{$filename}\"\n";
                                    $text .= "Content-Disposition: attachment; filename=\"{$filename}\"\n";
                                    $text .= "Content-Transfer-Encoding: base64\n";
                                    $text .= "\n";
                                    $text .= chunk_split(base64_encode(file_get_contents($file)));
                                    $text .= "--__BOUNDARY__--";

                                    $header .= "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
                                //    $header .= "Return-Path: " . $admin . " \n";
                                    $header .= "From: " . $admin ." \n";
                                //    $header .= "Sender: " . $head ." \n";
                                //    $header .= "Reply-To: " . $admin . " \n";
                                //    $header .= "Organization: " . $head . " \n";
                                //    $header .= "X-Sender: " . $head . " \n";
                                //    $header .= "X-Priority: 3 \n";
                                }else{
                                    $text = $body;
                                    $header .= "From: " . $admin ." \n";
                                }
/*
                                $pfrom = "-f$admin";
                                if(@mb_send_mail($email , $this->title , $text,$header,$pfrom)){
                                    Log::debug("Send_success=>".$email);
                                }else{
                                    Log::debug("Send_error=>".$email);
                                }
*/

                                $this->to = $email;
                                Mail::raw($body, function ($message) {
                                    $message->to($this->to)
                                        ->subject($this->title);
                                });
                                Log::debug("mailaddress=>".$email);
                                usleep(300);
                            }

                        }
                    }
                    $i++;
                }//while終わり
            }
            $request->session()->put('message', "アップロードに成功しました。");
            return redirect(route('admin.members.upload'));
        }
        $set['message'] = $request->session()->get('message',[]);
        $request->session()->forget('message');
        return view('admin.members.upload', $set);
    }

    private function validateUploadFile(Request $request)
    {
        return \Validator::make($request->all(), [
                'csv_file' => 'required|file|mimetypes:text/plain|mimes:csv,txt',
            ], [
                'csv_file.required'  => 'ファイルを選択してください。',
                'csv_file.file'      => 'ファイルアップロードに失敗しました。',
                'csv_file.mimetypes' => 'ファイル形式が不正です。',
                'csv_file.mimes'     => 'ファイル拡張子が異なります。',
            ]
        );
    }

    public function template(Request $request){

        // カラムの作成
        $head = [
            'ID',
            '会員区分',
            'ログインID',
            'パスワード',
            '会員番号',
            '氏名(姓)',
            '氏名(名)',
            '氏名(せい)',
            '氏名(めい)',
            '電話番号',
            'メールアドレス',
            '所属',
            '所属住所'
        ];

        $usertype = config('pacd.user.type');
        $userlist = [];
        //ユーザー情報取得
        $user = User::orderBy('type',"asc")->get();
        $i=0;
        foreach($user as $value){
            $userlist[$i][] = $value->id;
            $userlist[$i][] = $usertype[$value->type];
            $userlist[$i][] = $value->login_id;
            $userlist[$i][] = "";
            $userlist[$i][] = $value->type_number;
            $userlist[$i][] = $value->sei;
            $userlist[$i][] = $value->mei;
            $userlist[$i][] = $value->sei_kana;
            $userlist[$i][] = $value->mei_kana;
            $userlist[$i][] = $value->tel;
            $userlist[$i][] = $value->email;
            $userlist[$i][] = $value->busyo;
            $userlist[$i][] = $value->address;
            $i++;
        }
        CommonEventClass::createCsv($userlist,$head);
        exit();
    }

    //年会費
    public function yearPayment(Request $request){

        $year = ($request->year)?$request->year:date("Y");
        $yearpayment = Yearpayment::where(['year'=>$year])->first();

        $set = [];

        $set['selectyear'] = $year;
        $set['message'] = $request->session()->get('message');
        $set['invoice_address'] = ($yearpayment->invoice_address)??config('pacd.bank.invoice_address');
        $set['invoice_memo'] = ($yearpayment->invoice_memo)??config('pacd.bank.invoice_memo');
        $set['bank_name'] = ($yearpayment->bank_name)??config('pacd.bank.name');
        $set['bank_code'] = ($yearpayment->bank_code)??config('pacd.bank.code');
        $set['recipe_memo'] = ($yearpayment->recipe_memo)??config('pacd.bank.recipe_memo');
        $request->session()->forget('message');
        return view('admin.members.yearPayment', $set);
    }
    public function yearPaymentRegist(Request $request){

        $year = $request->year;
        $yearpaymentdata = Yearpayment::where(['year'=>$year])->first();
        if($yearpaymentdata){
            $yearpayment = Yearpayment::where(['id'=>$yearpaymentdata->id])->first();
        }else{
            $yearpayment = Yearpayment::create($request->all());
        }
        $yearpayment->fill($request->all())->save();


        $request->session()->flash('message', "請求書・領収書情報の登録を行いました。");
        $route = route('admin.members.yearPayment')."?year=".$year;
        return redirect($route);
    }

    // 会員一覧ページにリダイレクト
    private function redirectIndex() {
        return redirect(route('admin.members.index'));
    }
}
