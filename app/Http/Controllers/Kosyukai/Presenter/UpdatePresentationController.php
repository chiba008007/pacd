<?php

namespace App\Http\Controllers\Kosyukai\Presenter;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Mailforms;
use App\Models\User;

class UpdatePresentationController extends Controller
{
    private $form;
    private $category;
    private $presentation;
    private $attendee;

    public function __construct()
    {
        $current = Route::current();
        if ($current) {
            // 登録されていない講演の場合404エラー
            if (!($this->presentation = Presentation::find($current->presentation_id))) {
                abort(404);
            }
            // 講習会の講演でない場合404エラー
            $this->form = config('pacd.form.type.kosyukai_presenter');
            $this->category = config('pacd.category.'.$this->form['category_prefix']);
            $this->attendee = $this->presentation->presenter->attendee;
            if ($this->attendee->event->category_type != $this->category['key']) {
                abort(404);
            }
        }
        $this->middleware('auth');
    }

    public function edit($presentation_id)
    {
        $this->checkPresenter();

        $set['title'] = $this->category['name'] . ' 講演内容編集';
        $set['form'] = $this->form;
        $set['presentation'] = $this->presentation;
        return view('presenter.edit_presentation', $set);
    }

    public function update($presentation_id, Request $request)
    {
        $this->checkPresenter();

        $rules = [
            'description' => 'nullable|string',
            'file.*' => 'nullable|file',
            'delete.*' => 'nullable|boolean',
        ];
        $attribute = [
            'number' => '発表番号',
            'description' => '講演内容',
            'file.proceeding' => '原稿',
            'file.flash' => '原稿',
            'file.poster' => '原稿',
        ];
        $request->validate($rules, [], $attribute);

        DB::beginTransaction();
        try {
            // ファイルアップロードまたは削除
            $dir = config('pacd.presentation_file.path') . '/'. $this->category['prefix'] . '/' . $this->presentation->number;
            foreach (config('pacd.presentation_file.type') as $type) {
                if (@$request->delete[$type] && $this->presentation->$type) {
                    // 削除
                    Storage::delete($this->presentation->$type);
                    $this->presentation->$type = '';
                } elseif (@$request->file[$type]) {
                    $file = $request->file("file.$type");
                    if ($file) {
                        // アップロード
                        if ($this->presentation->$type) Storage::delete($this->presentation->$type); // 登録済みファイル削除
                        $path = Storage::putFileAs($dir, $file, $file->getClientOriginalName());
                        $data[$type] = $path;
                    }
                }
            }
            $data['description'] = $request->description ?? '';

            $this->presentation->fill($data);
            $this->presentation->save();
            DB::commit();
            $this->mailsend($this->attendee->user->id);
            Log::info("【" . $this->category['name']. "講演内容編集】presentation_id:$presentation_id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . "【" . $this->category['name'] . "講演内容編集】presentation_id: $presentation_id, error:" . $e->getMessage());
            return redirect()->back()->withInput()->with('status', $this->category['name'] . '講演内容を更新できませんでした。');
        }

        return redirect()->back()->with('status', $this->category['name'] . '講演内容を更新しました。');
    }


    //メール配信
    public function mailsend($userid){
        //例会参加メール取得
        $form_type = config('pacd.CONST_MAIL_FORM_TEMP.kosyukai.9.key');
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
        //プレゼンテーションデータ取得
        $body = preg_replace("/##upload1##/",$this->presentation->number,$body);
        $body = preg_replace("/##upload2##/",$this->presentation->description,$body);

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



    private function checkPresenter() {
        // ログイン中ユーザーの講演でない場合エラー
        if (Auth::id() != $this->attendee->user->id) {
            abort(404);
        }
    }
}
