<?php

namespace App\Http\Controllers\Kosyukai\Attendee;

use App\Http\Controllers\Controller;
use App\Library\PagesLibrary;
use App\Mail\Admin\CreateAttendee;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\FormDataAttendee;
use App\Models\FormInput;
use App\Models\Page;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UpdateController extends Controller
{
    private $form;
    private $attendee;

    public function __construct()
    {
        $this->middleware('auth');

        $this->form = config('pacd.form.type.kosyukai_attendee');

        $current = Route::current();
        if ($current) {
            $this->attendee = Attendee::find($current->attendee_id);

            // 参加者情報が取得できない場合404エラー
            if (!$this->attendee) {
                abort(404);
            }
            // 例会の参加者でない場合エラー
            if ($this->attendee->event->category_type != config('pacd.category.kosyukai.key')) {
                abort(404);
            }
        }
    }

    // 参加者情報編集ページ表示
    public function edit($attendee_id)
    {
        $this->checkAttendee();

        $set['attendee'] = $this->attendee;
        $set['form'] = $this->form;
        $set['title'] = $this->form['display_name'] . '情報編集';
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_published' => true])->get();
        $set['event'] = $this->attendee->event;
        $set['user'] = $this->attendee->user;

        return view('attendee.edit', $set);
    }

    // 参加者情報更新処理
    public function update($attendee_id, Request $request)
    {
        $this->checkAttendee();

        $rules['event_join_id'] = 'nullable|exists:event_joins,id';
        if ($request->custom) {
            // カスタムインプット項目がある場合、バリデーション実行
            $rules['custom.*'] = new CustomFormDataRule();
        }
        $request->validate($rules);

        // データ更新
        DB::beginTransaction();
        try {
            $this->attendee->event_join_id = $request->event_join_id;
            if(isset($request->event_join_id_list)){
                $this->attendee->event_join_id_list = implode(",",$request->event_join_id_list);
            }
            $this->attendee->save();
            if ($request->custom) {
                FormDataAttendee::updateFromInputData($request->custom, $this->attendee);
                Log::info("【". $this->form['display_name']. "情報編集】attendee_id:$this->attendee->id");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【". $this->form['display_name'] . "情報編集】attendee_id: $this->attendee->id, error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('status', '参加者情報を更新できませんでした。');
        }

        return redirect()->back()->with('status', '参加者情報を更新しました。');
    }



    // 更新可能な参加者情報か
    private function checkAttendee() {
        // ログイン中のユーザーの参加者情報でない場合エラー
        if ($this->attendee->user_id != Auth::id()) {
            abort(404);
        }
    }
}
