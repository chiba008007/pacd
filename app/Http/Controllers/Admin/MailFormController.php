<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Mailforms;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleNotification;
use App\Models\FormInput;

class MailFormController extends Controller
{
    //
    public function __construct()
    {
        $this->category_prefix = config('pacd.category.mailform.prefix');
        $this->category_title = config('pacd.category.mailform.name');
    }
    public function index($key="",$formtype){
        //フォームタイプのキーを指定
        $formtypekey = config('pacd.CONST_FORM_TYPE_KEY');
        $form_type_key = $formtypekey[$key];

        $set = [];
        $set['title'] = $this->category_title;
        $set['pages'] = [];
        $set['key'  ] = $key;
        $set['category_prefix'] = $this->category_prefix;
        $form_temp = config('pacd.CONST_MAIL_FORM_TEMP');
        $set['CONST_MAIL_FORM_TEMP'] = $form_temp[$key];
        $form_replace = config('pacd.CONST_MAIL_REPLACE');
        $set['CONST_MAIL_REPLACE'] =$form_replace['member'];
        $set['CONST_MAIL_REPLACE_UPLOAD'] =$form_replace['upload'];
        $set['CONST_MAIL_REPLACE_JOIN'] =$form_replace['join'];
        //フォームデータの取得
        $form_type = FormInput::whereIN("form_type",$form_type_key)->get();
        $set['form_type'] = $form_type;
        return view('admin.mails.index', $set);
    }
    /***********
     * 更新
     */
    public function edit(Request $request){

        if(Mailforms::editData($request)){
            return redirect()->back()->with('status', 'メールフォーム編集を行いました');
        }else{
            return redirect()->back()->with('status', 'メールフォーム編集に失敗しました');
        }
    }
    /*********
     * データ取得
     */
    public function getAjax($id="",Request $request){
        $data = Mailforms::where("form_type",$request->form_type)->where("status",1)->first();
        header('Content-Type: application/json; charset=utf-8');
        $return['title'] = $data->title;
        $return['note'] = $data->note;
        $return['id'] = $id;
        $return['form_type'] = config('pacd.CONST_MAIL_FORM_TEMP.'.$id.'.'.$data->form_type.'.connect');
        echo json_encode($return);
        exit();
    }
}
