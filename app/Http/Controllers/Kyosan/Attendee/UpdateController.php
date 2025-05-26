<?php

namespace App\Http\Controllers\Kyosan\Attendee;

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
use App\Models\kyosanTitle;
use App\Models\Event_join;
class UpdateController extends Controller
{
    private $form;
    private $attendee;

    public function __construct()
    {
        $this->middleware('auth');

        $this->form = config('pacd.form.type.kyosan_attendee');

        $current = Route::current();
        if ($current) {
            $this->attendee = Attendee::find($current->attendee_id);

            // 参加者情報が取得できない場合404エラー
            if (!$this->attendee) {
                abort(404);
            }
            // 例会の参加者でない場合エラー
            if ($this->attendee->event->category_type != config('pacd.category.kyosan.key')) {
                abort(404);
            }
        }
    }

    // 参加者情報編集ページ表示
    public function edit($attendee_id)
    {
        $this->checkAttendee();

        $event_joins = Event_join::
            select(["pattern","event_id","join_price"])
            ->where([
                "event_id" => $this->attendee->event_id, "status" => 1, "join_status" => 1
            ])->get();

        $join_money = [];
        foreach($event_joins as $value){
            $join_money[$value->pattern] = $value;
        }

        $set['attendee'] = $this->attendee;
        $set['form'] = $this->form;
        $set['title'] = $this->form['display_name'] . '情報編集';
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_published' => true])->get();
        $set['event'] = $this->attendee->event;
        $set['user'] = Auth::user();
        $set['join_money'] = $join_money;
        $set['kyosanTitle'] = kyosanTitle::first();
        return view('attendee.edit', $set);
    }

    // 参加者情報更新処理
    public function update($attendee_id, Request $request)
    {
        $this->checkAttendee();
        $rules['event_join_id'] = 'nullable|exists:event_joins,id';

        if ($request->has('custom') && is_array($request->input('custom'))) {
            $rules['custom.*'] = ['nullable', new CustomFormDataRule()];
        }

        $request->validate($rules);

        // データ更新
        DB::beginTransaction();
        try {
            $this->attendee->event_join_id = $request->event_join_id;
            if(isset($request->event_join_id_list)){
                $this->attendee->event_join_id_list = implode(",",$request->event_join_id_list);
            }

            $this->attendee->discountSelectFlag = $request->discountSelectFlag;
            $this->attendee->discountSelectText = $request->discountSelectText;

            $this->attendee->tenjiSanka1Status = $request->tenjiSanka1Status;
            $this->attendee->tenjiSanka1Name = $request->tenjiSanka1Name;
            $this->attendee->tenjiSanka1Money = $request->tenjiSanka1Money;
            $this->attendee->tenjiSanka2Status = $request->tenjiSanka2Status;
            $this->attendee->tenjiSanka2Name = $request->tenjiSanka2Name;
            $this->attendee->tenjiSanka2Money = $request->tenjiSanka2Money;
            $this->attendee->konsinkaiSanka1Status = $request->konsinkaiSanka1Status;
            $this->attendee->konsinkaiSanka1Name = $request->konsinkaiSanka1Name;
            $this->attendee->konsinkaiSanka1Money = $request->konsinkaiSanka1Money;
            $this->attendee->konsinkaiSanka2Status = $request->konsinkaiSanka2Status;
            $this->attendee->konsinkaiSanka2Name = $request->konsinkaiSanka2Name;
            $this->attendee->konsinkaiSanka2Money = $request->konsinkaiSanka2Money;

            $this->attendee->paydate = $request->paydate;

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
