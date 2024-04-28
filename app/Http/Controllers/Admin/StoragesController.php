<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pdfstorage;
use Barryvdh\DomPDF\PDF;

class StoragesController extends Controller
{
    public function getLists($request){
        $lists = Pdfstorage::where([['status','=',1]]);
        if($request->session()->get('fromDate')){
            $lists->where([['create_date','>=',$request->session()->get('fromDate')]]);
        }
        if($request->session()->get('toDate')){
            $lists->where([['create_date','<=',$request->session()->get('toDate')]]);
        }
        if($request->session()->get('filename')){
            $lists->where([['filename','like',"%".$request->session()->get('filename')."%"]]);
        }
        return $lists;
    }
    //
    public function list($page = 0,Request $request){

        $limit = 30;
        // 一覧データ取得
        $lists = self::getLists($request);

        $total = ceil($lists->count()/$limit);
        $offset = $limit*$page;
        $result = $lists->offset($offset)
        ->limit($limit)
        ->orderby('id',"desc")
        ->get();

        $set = [];
        $set[ 'lists' ] = $result;
        $set[ 'total' ] = $total;
        $set[ 'request' ] = $request;
        return view('admin.storage.index', $set);
    }
    public function setsession(Request $request){
        $request->session()->put('fromDate', $request->input( 'fromDate' ));
        $request->session()->put('toDate', $request->input( 'toDate' ));
        $request->session()->put('filename', $request->input( 'filename' ));
        return redirect()->route('admin.members.storages');
    }
    public function delete($id){
        $data = Pdfstorage::find($id);
        $data->status = 0;
        $data->save();
        session()->flash('flash_message', 'ファイルの削除を行いました。');
        return redirect()->route('admin.members.storages');
    }
    public function download($id){
        $list = Pdfstorage::where(
        [
        'id'=>$id,
        'status'=>1
        ])->first();
        $filenamecode = storage_path('pdf/search/'.$list->filenamecode.".pdf");
        $filename = $list->filename.".pdf";
        
        $headers = [
            'Content-Type' => 'application/pdf'
        ];
        return response()->download($filenamecode, $filename, $headers);
    }
    public function setdown(Request $request){
        // 対象のデータを取得
        $lists = self::getLists($request);

        // 保存用フォルダの中にあるファイルを削除
        $saveDir = storage_path("pdf/temp/");
        $glob = glob($saveDir."*");
        foreach($glob as $value){
             unlink($value);
        }
        // 保存用フォルダ作成
        $originalDir = storage_path("pdf/original/");
        // ファイルのコピー
        //$glob = glob($originalDir."*");
        $glob = $lists->get();
        foreach($glob as $value){
            copy($originalDir.$value->filename.'.pdf', $saveDir.$value->filename.'.pdf');
        }
        // zipファイル作成
        $zipfilename = "zipfiles".date('YmdHis');
        $zip = "zip -j ".$zipfilename." ".$saveDir."*";
        exec($zip);
        // zipファイルのダウンロード
        header("Content-type: application/zip");
        header('Content-Disposition:attachment;filename = "'.$zipfilename.'.zip"');
        header('Content-Length: '.filesize($zipfilename.".zip"));
        echo file_get_contents($zipfilename.'.zip');

        exit();
    }
}
