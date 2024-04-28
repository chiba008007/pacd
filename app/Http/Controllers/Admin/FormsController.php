<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFormInputRequest;
use App\Library\PagesLibrary;
use Illuminate\Http\Request;
use App\Models\FormInput;
use App\Models\FormInputValue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;
use App\Models\Attendee;

class FormsController extends Controller
{
    private $form = '';

    public function __construct()
    {
        // ルートパラメータチェック
        $current = Route::current();

        if ($current) {
            $this->form = config('pacd.form.type.' . $current->form_prefix ?? '');
            if (!$this->form || $this->form['category_prefix'] != $current->category_prefix) {
                abort(404);
            }
        }
    }

    // フォーム項目一覧ページ表示
    public function index()
    {

        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '登録フォーム 一覧';
        $set['inputs'] = FormInput::select(
                "form_inputs.*",
                "events.name as event_name"
                )->where('form_inputs.form_type', $this->form['key'])
                ->leftJoin('events', 'events.id', '=', 'form_inputs.event_id')
                ->get();
        return view('admin.forms.index', $set);
    }

    // フォームプレビューページ表示
    public function preview($category_prifix, $form_prefix, Request $request)
    {
        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '登録フォーム プレビュー';
        $page = PagesLibrary::getContents(['route_name' => $this->form['prefix']]);
        if ($this->form['prefix'] != 'register') {
            $set['form']  = config('pacd.form.type.' . $page['route_name']);
            // 固定フォームプレビュー用テストデータ
            $set['user'] = User::newTestData();
            $set['event'] = Event::newTestData();
            $set['attendee'] = Attendee::newTestData();
        }

        $preview = $request->session()->get('preview');
        if (isset($preview['action']) && $preview['action'] == 'update') {
            // 登録済みデータを入力値に置き換え
            $page['inputs'][$preview['form_input_id']] = new FormInput($preview);
            foreach ($preview['value'] as $key => $val) {
                $vals[] = new FormInputValue([
                    'form_input_id' => $preview['form_input_id'],
                    'value' => $val,
                    'is_included_textarea' => $preview['is_included_textarea'][$key],
                ]);
            }
            $page['inputs'][$preview['form_input_id']]->values = $vals ?? [];
        }
        $set['preview'] = $preview;
        $set['page'] = $page;

        return view('admin.forms.preview', $set);
    }

    // フォーム項目新規登録ページ表示
    public function create($category_prifix, $form_prefix)
    {
        //イベントデータ取得
        $key = config('pacd.category.'.$category_prifix.".key");
        $event = Event::getEventDataType($key);

        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '登録フォーム 新規登録';
        $set['eventlists'] = $event;
        return view('admin.forms.create', $set);
    }

    // フォーム項目新規登録処理
    public function store2($category_prifix, $form_prefix, Request $request)
    {
var_dump($request);
exit();
    }
  //  public function store($category_prifix, $form_prefix, UpdateFormInputRequest $request)
    public function store($category_prifix, $form_prefix, Request $request)
    {
        if ($request->action == 'preview') {
            // プレビュー
            $request->merge(['action' => 'register']);
            return redirect(route('admin.form.preview', [$category_prifix, $form_prefix]))->with('preview', $request->all());
        } elseif ($request->action == 'register') {

            // 登録
            DB::beginTransaction();
            try {
                if ($formInput = FormInput::create($request->all())) {
                    foreach ($request->value as $key => $value) {
                        FormInputValue::create([
                            'form_input_id' => $formInput->id,
                            'value' => $value ?? '',
                            'is_included_textarea' => $request->is_included_textarea[$key],
                            'csvflag' => $formInput->csvflag,
                            'csvtag' => $formInput->csvtag,
                        ]);
                    }
                    DB::commit();
                    Log::info('【' . $this->form['display_name'] . '登録フォーム項目追加】form_input_id：' . $formInput->id);
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error(Route::currentRouteAction() . '【' . $this->form['display_name'] . '項目追加】error:' . $e->getMessage());
                return redirect()->back()->withInput()->with('flash.error', '登録フォーム項目の登録に失敗しました。');
            }
        }
        return $this->redirectIndex()->with('flash.success', '登録フォーム項目を登録しました。');
    }

    // フォーム項目編集ページ表示
    public function edit($category_prifix, $form_prefix, $id)
    {

        //イベントデータ取得
        $key = config('pacd.category.'.$category_prifix.".key");
        $event = Event::getEventDataType($key);

        $set = $this->getMeta();
        $set['title'] = $this->form['display_name'] . '登録フォーム 編集';
        $set['input'] = FormInput::find($id);
        $set['eventlists'] = $event;
        return view('admin.forms.edit', $set);
    }

    // フォーム項目更新処理
    public function update($category_prifix, $form_prefix, $id, UpdateFormInputRequest $request)
    {
        if ($request->action == 'preview') {
            // プレビュー
            $request->merge(['action' => 'update', 'form_input_id' => $id]);
            return redirect(route('admin.form.preview', [$category_prifix, $form_prefix]))->with('preview', $request->all());
        } elseif ($request->action == 'update') {
            // 更新
            DB::beginTransaction();
            try {
                FormInput::find($id)->update($request->all());
                $values = FormInputValue::where('form_input_id', $id)->get();


                for ($i=0; $i < $request->count; $i++) {
                    $data = [
                        'form_input_id' => $id,
                        'value' => $request->value[$i],
                        'is_included_textarea' => $request->is_included_textarea[$i],

                    ];
                    // dd($data);
                    if (isset($values[$i])) {           // 更新
                        $values[$i]->update($data);
                    } else {
                        FormInputValue::create($data);  // 追加
                    }
                }
                for (; $i < $values->count(); $i++) {   // 削除
                    $values[$i]->delete();
                }
                DB::commit();
                Log::info('【' . $this->form['display_name'] . "フォーム項目編集】form_input_id: $id");
            } catch (\Exception $e) {
                DB::rollback();
                Log::error(Route::currentRouteAction() . '【' . $this->form['display_name'] . 'フォーム項目編集】form_input_id: ' . $id . ', error:' . $e->getMessage());
                return redirect()->back()->withInput()->with('flash.error', 'フォーム項目の更新に失敗しました。');
            }
        }

        return $this->redirectIndex()->with('flash.success', 'フォーム項目を更新しました。');
    }

    // フォーム項目削除処理
    public function destroy($category_prifix, $form_prefix, $id)
    {
        DB::beginTransaction();
        try {
            FormInput::find($id)->delete();
            FormInputValue::where('form_input_id', $id)->delete();
            DB::commit();
            Log::info('【' . $this->form['display_name'] . "フォーム項目削除】form_input_id: $id");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(Route::currentRouteAction() . '【' . $this->form['display_name'] . 'フォーム項目削除】form_input_id: ' . $id . ', error:' . $e->getMessage());
            return redirect()->back()->with('flash.error', 'フォーム項目の削除に失敗しました。');
        }

        return $this->redirectIndex()->with('flash.success', 'フォーム項目を削除しました。');
    }

    // １つ前のページに戻る
    public function back($category_prifix, $form_prefix, Request $request)
    {

        if (isset($request->back_url)) {
            return redirect($request->back_url)->withInput();
        }
        return $this->redirectIndex();
    }



    // フォーム項目一覧ページにリダイレクト
    private function redirectIndex() {
        return redirect(route('admin.form.index', [$this->form['category_prefix'], $this->form['prefix']]));
    }

    // メタデータを返す
    private function getMeta() {
        return [
            'breadcrumbs' => [
                [
                    'title' => $this->form['display_name'] . '登録フォーム 一覧',
                    'url' => route('admin.form.index', [$this->form['category_prefix'], $this->form['prefix']]),
                ]
            ],
            'form' => $this->form
        ];
    }
}
