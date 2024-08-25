<?php

namespace App\Http\Controllers\Reikai\Attendee;

use App\Http\Controllers\Controller;
use App\Library\PagesLibrary;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\FormDataAttendee;
use App\Models\FormInput;
use App\Models\FormInputValue;
use App\Models\Page;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\User;

class RegisterController extends Controller
{
    private $event;
    private $form;
    private $user;

    public function __construct()
    {
        $this->middleware('auth');

        $this->form = config('pacd.form.type.reikai_attendee');

        $current = Route::current();
        if ($current) {
            $this->event = Event::where('code', $current->event_code)->first();

            // 申し込みできないイベントの場合404エラー
            if (!$this->event || !$this->event->isEnabled() || $this->event->category_type != config('pacd.category.reikai.key')) {
                abort(404);
            }
        }
    }

    // 参加者登録ページ表示
    public function create($event_code)
    {

        // 無料会員は例会参加不可
        $this->user = Auth::user();
        //各イベントに参加申込できるクラス
        //法人会員・個人会員
        // 0329type=1を追加
        /**
         * 参加申込が可能な期間を3月26日（火）～　4月21日（日）とし、4月22日(月)からは、非会員・協賛企業・協賛企業(窓口担当)　は参加できないように設定
         */
        $today = date('Y-m-d');
        if ($today > '2024-03-26' && $today <= '2024-04-21') {

            if (!(

                $this->user->type == 1 ||
                $this->user->type == 5 ||
                $this->user->type == 6 ||
                $this->user->type == 2 ||
                $this->user->type == 3 ||
                $this->user->type == 4)) {
                $joins = [
                    config('pacd.user.type')[1],
                    config('pacd.user.type')[5],
                    config('pacd.user.type')[6],
                    config('pacd.user.type')[2],
                    config('pacd.user.type')[3],
                    config('pacd.user.type')[4]
                ];

                $page = Page::where(['route_name' => $this->form['prefix']])->first();
                $set['title'] = $page->title;
                $set['member'] = implode("/", $joins);
                return view('attendee.attention_authentication', $set);
            }
        } else {
            if (
                !(

                    $this->user->type == 2 ||
                    $this->user->type == 3 ||
                    $this->user->type == 4)
            ) {
                $joins = [
                    config('pacd.user.type')[2],
                    config('pacd.user.type')[3],
                    config('pacd.user.type')[4]
                ];

                $page = Page::where(['route_name' => $this->form['prefix']])->first();
                $set['title'] = $page->title;
                $set['member'] = implode("/", $joins);
                return view('attendee.attention_authentication', $set);
            }
        }
        // 既に申し込み済みの場合、参加者マイページへリダイレクト
        $attendee = Attendee::where(['event_id' => $this->event->id, 'user_id' => $this->user->id])->first();
        if ($attendee) {
            return redirect(route('mypage.' . $this->form['category_prefix']));
        }

        $set = PagesLibrary::getContents(['route_name' => $this->form['prefix']], $this->event->id);
        $set['event'] = $this->event;
        $set['form'] = $this->form;
        $set['user'] = $this->user;
        return view('attendee.register', $set);
    }

    // 参加者登録処理
    public function store($event_code, Request $request)
    {

        // 無料会員は例会参加不可
        $this->user = Auth::user();

        // if ($this->user->type == 1) {
        //     $page = Page::where(['route_name' => $this->form['prefix']])->first();
        //     $set['title'] = $page->title;
        //     return view('attendee.attention_authentication', $set);
        // }

        // 既に申し込み済みの場合、参加者マイページへリダイレクト
        $attendee = Attendee::where(['event_id' => $this->event->id, 'user_id' => $this->user->id])->first();
        if ($attendee) {
            return redirect(route('mypage.' . $this->form['category_prefix']));
        }
        $rules['event_join_id'] = 'nullable|exists:event_joins,id';
        if ($request->custom) {
            // カスタムインプット項目がある場合、バリデーション実行
            $rules['custom.*'] = new CustomFormDataRule();
        }
        $request->validate($rules);


        // データ登録
        DB::beginTransaction();
        try {
            $data['event_id'] = $this->event->id;
            $data['user_id'] = $this->user->id;


            if ($request->event_join_id) {
                $data['event_join_id'] = $request->event_join_id;
            }

            if (!empty($request->event_join_id_list)) {
                $data['event_join_id_list'] = implode(",", $request->event_join_id_list);
            }
            //event_numberの取得
            $data['event_number'] = Event::getEventNumber($this->event->id);
            //ダウンロード不可の初期状態を可にする
            $data['is_enabled_invoice'] = 1;
            if($request->discountSelectFlag){
                $data['discountSelectFlag'] = $request->discountSelectFlag;
                $data['discountSelectText'] = $request->discountSelectText;
            }
            $attendee = Attendee::create($data);
            if ($request->custom) {
                FormDataAttendee::createFromInputData($request->custom, $attendee);
            }
            DB::commit();
            Log::info("【" . $this->form['display_name'] . "登録】attendee_id:$attendee->id");
            //メール配信
            $this->attendee = $attendee;
            $this->mailsend($this->user->id);
            // 管理者へメール通知
            //Mail::to(config('admin.email'))->send(new \App\Mail\Admin\CreateAttendee($attendee, $this->form));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【" .  $this->form['display_name'] . "登録】user_id:" . $this->user->id . ", error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('status', '参加申し込みできませんでした。');
        }

        $page = Page::where('route_name', $this->form['prefix'])->first();
        $set['title'] = $page->title . ' 受付完了';
        $set['edit_url'] = route('reikai_attendee.edit', [$attendee->id]);
        $set['create_url'] = route('reikai_presenter', [$attendee->id]);
        $set['userdata'] = $this->user;
        $set['prefix'] = $this->form['prefix'];
        $set['event'] = $this->event;
        return view('attendee.complete', $set);
    }

    //メール配信
    public function mailsend($userid)
    {
        //例会参加メール取得
        $form_type = config('pacd.CONST_MAIL_FORM_TEMP.reikai.1.key');
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
        //イベント参加番号置き換え
        $event_number = sprintf("%010d", $this->attendee->event_number);
        $body = preg_replace("/##event_number##/", $event_number, $body);

        foreach (config('pacd.CONST_MAIL_REPLACE.member') as $key => $value) {
            $body = preg_replace("/" . $value['replace'] . "/", $userdata->$key, $body);
        }
        $forminput = FormInput::where(['form_type' => config('pacd.form.type.reikai_attendee.key')])->get();
        $attendee = Attendee::where(['event_id' => $this->event->id, 'user_id' => $this->user->id])->first();

        // var_dump($attendee);
        foreach ($forminput as $key => $value) {
            //登録フォームの値
            $forminputvalue = FormInputValue::where(['form_input_id' => $value->id])->get();
            $write = "";
            foreach ($forminputvalue as $k => $val) {

                $form_data_attendees = FormDataAttendee::where(['attendee_id' => $attendee->id])
                    ->where(["form_input_value_id" => $val->id])
                    ->first();
                if (!$form_data_attendees['data']) continue;
                $write .= $form_data_attendees['data'] . $form_data_attendees['data_sub'] . " ";
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

        mb_send_mail($this->to, $this->title, $body, $header, $pfrom);

        Log::debug($this->event->name . "【メール配信：" . $this->to . "\n:本文:" . $body);


        return true;
        /*
        //メール配信
        Mail::raw($body, function ($message) {
            $message->to($this->to)
                ->subject($this->title);
        });
*/
    }
}
