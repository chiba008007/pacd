<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __construct()
    {
      //  $this->middleware('auth');
    }
    public function index($id=""){

        $upload = Upload::find($id);
        if(isset($_REQUEST[ 'pw' ]) && $_REQUEST[ 'pw' ] == "off"){
            $filename = time().".".$upload->ext;
            $headers = ['Content-Type' => 'text/plain'];
            return Storage::download('public/'.$upload->filename.".".$upload->ext, $filename, $headers);
        }

        if(isset($_REQUEST[ 'pw' ]) && $_REQUEST[ 'pw' ] == $upload->lockkey){
            $filename = time().".".$upload->ext;
            $headers = ['Content-Type' => 'text/plain'];
            return Storage::download('public/'.$upload->filename.".".$upload->ext, $filename, $headers);
        }else{
            return redirect()->back()->with('status', 'ファイルのダウンロードに失敗しました。');

        }

    }
}
