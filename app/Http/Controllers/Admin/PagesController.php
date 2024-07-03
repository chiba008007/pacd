<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PageContent;
use App\Models\PageSubContent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Library\PagesLibrary;
use App\Models\Event;
use App\Models\User;
use App\Models\Attendee;
use App\Models\banner;
use Illuminate\Support\Facades\DB;
use App\Models\banner_setting;
use App\Models\InquireSetting;
use App\Models\Url;
use App\Models\eventPassword;
use Illuminate\Support\Facades\Route;

class PagesController extends Controller
{

    public $eventtype = "pages";
    public function passwordcheck(){
        $currentPath= Route::getFacadeRoot()->current()->uri();
        $set['currentPath'] = $currentPath;
        $set['eventtypepath'] = $this->eventtype;
        return view('admin.members.eventtypeForm', $set);
    }
    public function passwordchecked(Request $request){
        $data = eventPassword::where("eventtype","=",$this->eventtype)
        ->where("password","=",$request->password)
        ->count();
        if($data > 0){
            // sessionに登録
            $request->session()->put($this->eventtype, true);
            return redirect(route('admin.'.$this->eventtype.'.index'));
        }
        return redirect()->back()->withInput()->with('flash.error', '認証に失敗しました。');
    }
    public function checked(){
        $data = session($this->eventtype);
        if(!$data){
            $url = url('/') . "/" . config("admin.uri")."/".$this->eventtype."/password/check";
            return redirect()->to($url)->send();
        } 
    }

    /**
     * 公開一覧ページ表示
     *
     * @return view
     */
    public function index()
    {
        $this->checked();

        $set['title'] = 'ページ一覧';
        $set['pages'] = Page::orderByDesc('is_opened')->orderBy('id')->paginate(20);

        return view('admin.pages.index', $set);
    }

    /**
     * 公開設定変更
     *
     * @param Request $request
     */
    public function open(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_opened' => ['required', 'boolean'],
        ]);
        if (!$validator->fails()) {
            $page = Page::find($request->id);
            if ($page && !$page->is_form) { // ※ 会員、参加者、講演者登録ページは公開設定変更不可
                $page->is_opened = $request->is_opened;
                if ($page->save()) {
                    Log::info("【公開ページ編集】pages.id:$request->id is_opened:$request->is_opened");
                    // キャッシュクリア
                    $artisan = config('admin.artisan');
                    exec("php $artisan route:cache", $output, $retval);
                    return redirect()->back()->with('status', '公開設定を変更しました。');
                }
            }
        }

        return redirect()->back()->with('flash.error', '公開設定を変更できませんでした。');
    }


    /**
     * 編集ページ表示
     *
     * @param int $id : ページID
     * @return view
     */
    public function edit($id)
    {
        if (!($page = PagesLibrary::getContents(['id' => $id]))) {
            abort(404);
        }
        $set['title'] = '「' . $page['title'] . '」ページの編集';

        if ($page['is_form'] && $page['route_name'] != 'register') {
            $set['form']  = config('pacd.form.type.' . $page['route_name']);
            // 固定フォームプレビュー用テストデータ
            $set['user'] = User::newTestData();
            $set['event'] = Event::newTestData();
            $set['attendee'] = Attendee::newTestData();
        }

        $set['page'] = $page;
        return view('admin.pages.edit', $set);
    }
    /*****
     * 並び順
     *
     */
    public function editOrder($id,Request $request){
        if(count($request->display_order)){
            foreach($request->display_order as $key=>$value){
                PageContent::where('id',$key)->update([
                    'display_order'=>$value
                ]);
            }
        }
        if(isset($request->delete) && count($request->delete)){
            foreach($request->delete as $key=>$value){
                PageContent::where('id',$key)->delete();
            }
        }
        return back();
    }
    /**
     * ajaxでコンテンツ更新
     *
     * @param int $id: ページID
     * @param Request $request
     * @return void
     */
    public function updateAjax($id, Request $request)
    {
        $column_name = $request->column;
        $row = null;
        $log = "【公開ページ編集】pages.id:$id content:$request->content column:$column_name";

        if (!empty($request->sub_content_id)) {

            // サブコンテンツ
            if ($request->sub_content_id === 'new') {
                // 追加
                if ($request->content_id === 'new') {
                    // テーブル、リストを新規追加
                    $content = PageContent::create(['page_id' => $id, 'content_type' => $request->content_type ?? 'list']);
                } else {
                    // 既存のテーブル、リストに行を追加
                    $content = PageContent::find($request->content_id);
                }
                if ($content) {
                    $row = PageSubContent::create(['page_content_id' => $content->id, 'column_count' => $request->column_count ?? 2]);
                }
            } else {
                // 更新
                $row = PageSubContent::find($request->sub_content_id);
            }
            $log .= $row ? " page_sub_contents.id:$row->id" : '';

        } elseif (!empty($request->content_id)) {

            // コンテンツ
            if ($request->content_id === 'new') {
                // 追加
                $row = PageContent::create(['page_id' => $id]);
            } else {
                // 更新
                $row = PageContent::find($request->content_id);
            }
            $log .= $row ? " page_contens.id:$row->id" : '';

        } else {

            // タイトルの更新
            $row = Page::find($id);

        }

        // データ更新
        if ($row) {
            $row->$column_name = $request->content;
            if ($row->save()) {
                Log::info($log);
                return 'データ更新成功';
            }
        }

        return 'データ更新失敗';
    }

    /***********
     * バナー管理
     */
    public function bannar(){
        $status = [
            1=>"表示",
            0=>"非表示",
        ];
        $bs = DB::table('banner_setting')->first();
        $set = [];
        $set['title'] = 'バナー一覧';
        //データ取得
        $now = date("Y-m-d");
        $select = DB::table('banner')->orderBy('sort')->get();
        $set[ 'select' ] = $select;
        $set[ 'status' ] = $status;
        $set[ 'smooth' ] = ($bs->smooth)??"";
        return view('admin.pages.bannar', $set);

    }
    public function bannarSetting(Request $request){
        foreach($request->sort as $key=>$value){
            $bannar = Banner::find($key);
            $bannar->status=$request->status[$key];
            $bannar->sort=$value;
            $bannar->save();
        }

        $id = 1;
        $bs = DB::table('banner_setting')->where('id',$id)->first();
        if($bs){
            $bs = banner_setting::find($id);
        }else{
            $bs = new banner_setting;
        }
        $bs->id = $id;
        $bs->smooth = sprintf("%d",$request->smooth);
        $bs->save();
        return redirect()->back()->with('flash_message', '設定の変更を行いました。');
    }
    public function bannarnew($id=''){
        $select = [];
        if($id > 0 ){
            $select = DB::table('banner')->where('id',$id)->first();
        }

        $set = [];
        $set['title'] = 'バナー新規登録';
        $set[ 'select' ] = $select;


        return view('admin.pages.bannar_new', $set);
    }
    public function bannardel($id){
        $bannar = new banner();
        $bannar->where('id', $id)->delete();
        return redirect()->back()->with('flash_message', 'バナー広告を削除しました。');
    }
    public function bannarpost(Request $request){
        //データ取得
        $count = DB::table('banner')->get()->count();
        //dd($request->all('bannar'));
        $bannar = new banner();
        if($request->id > 0 ){
            $bannar = Banner::find($request->id);
        }else{
            $bannar->sort = $count+1;
        }
        if($request->file( 'bannar' )){
            $file_name = $request->file('bannar')->getClientOriginalName();
            $request->file('bannar')->storeAs('/public/bannar',$file_name);
            $bannar->filename = $file_name;
        }

        $bannar->url = $request->url;
        $bannar->startdate = ($request->startdate)??'2000-01-01';
        $bannar->enddate = ($request->enddate)??'2099-01-01';


        $bannar->save();
        return redirect()->back()->with('flash_message', 'バナー広告を更新しました。');

    }
    public function inquire(){

        $inquireSetting = InquireSetting::get();

        $set = [];
        $set[ 'title' ] = "問合せ先アドレス設定";
        $set[ 'inquireSetting' ] = $inquireSetting;

        return view('admin.pages.inquire', $set);

    }
    public function inquireset(request $request){
        $inquireSetting = new InquireSetting();

        $inquireSetting::insert([
            'name' => $request[ 'name' ]
            ,'email' => $request[ 'email' ]
            ,'created_at' => NOW()
            ,'updated_at' => NOW()
        ]);
        return redirect(route('admin.pages.inquire'));
    }
    public function inquiredel($id){
        $inquireSetting = new InquireSetting();
        $inquireSetting->destroy($id);
        return redirect(route('admin.pages.inquire'));
    }

    public function url(){
        $urls = Url::get();

        $set = [];
        $set[ 'title' ] = "URL設定";
        $set[ 'urls'  ] = $urls;

        return view('admin.pages.url', $set);
    }
    public function urlsetting(request $request){

        $url = new url();
        if($request->file( 'files' )){
            $file_name = $request->file('files')->getClientOriginalName();
            $request->file('files')->storeAs('/public/files',$file_name);
            $url->filename = $file_name;
        }
        $url->memo = $request->memo;
        $url->url = '';
        $url->save();

        return redirect(route('admin.pages.url'));
    }
    public function delete($id){
        Url::where('id', $id)->delete();
        return redirect(route('admin.pages.url'));


    }
}
