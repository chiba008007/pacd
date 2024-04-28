<?php

namespace App\Http\Controllers\Reikai\Presenter;

use App\Http\Controllers\Controller;
use App\Models\Presenter;
use App\Models\FormDataPresenter;
use App\Models\FormInput;
use App\Models\Page;
use App\Rules\CustomFormDataRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Presentation;

class UpdateController extends Controller
{
    private $form;
    private $presenter;
    private $attendee;
    private $event;
    private $user;

    public function __construct()
    {
        $this->middleware('auth');

        $this->form = config('pacd.form.type.reikai_presenter');

        $current = Route::current();
        if ($current) {
            // 講演者情報が取得できない場合404エラー
            $this->presenter = Presenter::find($current->presenter_id);
            if (!$this->presenter) {
                abort(404);
            }
            // 例会の参加者でない場合エラー
            $this->attendee = $this->presenter->attendee;
            $this->event = $this->attendee->event;
            if ($this->event->category_type != config('pacd.category.reikai.key')) {
                abort(404);
            }
            $this->presentation=Presentation::where('presenter_id',$this->presenter->id)->first();

        }
    }

    // 講演者情報編集ページ表示
    public function edit($presenter_id)
    {
        $this->checkAttendee();

        $set['presenter'] = $this->presenter;
        $set['attendee'] = $this->attendee;
        $set['form'] = $this->form;
        $set['title'] = $this->form['display_name'] . '情報編集';
        $set['inputs'] = FormInput::where(['form_type' => $this->form['key'], 'is_display_published' => 1])
        ->where('event_id', 0)
        ->orWhere(['event_id'=>$this->event->id])
        ->where(['form_type' => $this->form['key'], 'is_display_published' => 1])
        ->get();

        $set['event'] = $this->event;
        $set['user'] = $this->user;
        $set['presentation'] = $this->presentation;

        return view('presenter.edit', $set);
    }

    // 講演者情報更新処理
    public function update($presenter_id, Request $request)
    {
        $this->checkAttendee();

        //プレゼンテーション用ページ更新
        Presentation::where('id',$this->presentation->id)
        ->update([
            'description'=>$request->description
        ]);

        // カスタムインプット項目のみ更新可能
        if ($request->custom) {
            // バリデーション実行
            $request->validate(['custom.*' => new CustomFormDataRule()]);

            // データ更新
            DB::beginTransaction();
            try {
                if ($request->custom) {
                    FormDataPresenter::updateFromInputData($request->custom, $this->presenter);
                    DB::commit();
                    Log::info("【". $this->form['display_name']. "情報編集】presenter_id:$this->presenter->id");
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error(Route::currentRouteAction() . "【". $this->form['display_name'] . "情報編集】presenter_id: $this->presenter->id, error:" . $e->getMessage());
                return redirect()->back()->withInput()->with('status', '講演者情報を更新できませんでした。');
            }
        }

        return redirect()->back()->with('status', '講演者情報を更新しました。');
    }

    public function complete($presenter_id)
    {
        $page = Page::where('route_name', $this->form['prefix'])->first();
        $set['title'] = $page->title . ' 受付完了';
        $set['create_url'] = route('reikai_presenter', $this->attendee->id);
        $set['edit_url'] = route('reikai_presenter.edit', $this->presenter->id);

        return view('presenter.complete', $set);
    }



    // 更新可能な講演者か
    private function checkAttendee() {
        // ログイン中のユーザーの参加者情報でない場合エラー
        $this->user = Auth::user();
        $this->attendee = $this->presenter->attendee;
        if ($this->attendee->user_id != $this->user->id) {
            abort(404);
        }
    }
}
