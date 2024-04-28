<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendee;

class ZipController extends Controller
{
    //
    public function index($event_id,$type,$date,$flag = 0 ){
        $sql = "
        SELECT
            pre.description,
            pre.proceeding,
            pre.flash,
            pre.poster,
            pre.proceeding_flag,
            pre.flash_flag,
            pre.poster_flag,
            pl.disp_status1,
            pl.disp_status2,
            pl.disp_status3
            FROM
            programs as p
            LEFT JOIN program_lists as pl ON p.id = pl.program_id
            LEFT JOIN presentations as pre ON pre.id = pl.presentation_id

        ";
        if($event_id == 0 ){
            $data = DB::select($sql,[]);
        }else
        if($event_id && !$date && !$type){
            $sql .= "
                WHERE p.event_id = ?
            ";
            $data = DB::select($sql,[$event_id]);

        }else{
            $sql .= "
                WHERE p.event_id = ?
                AND p.date = ?
                AND p.type=?
            ";
            $data = DB::select($sql,[$event_id,$date,$type]);
        }
        if(!Auth::user()){
            echo "not use";
            exit('not');
        }
        /*
        $attendee = Attendee::where('user_id',Auth::user()->id)->where('event_id',$event_id)->first();
        if($attendee->is_paid != 1){
            echo "no pay";
            exit();
        }
        */
        //からフォルダの作成
        $basepath = public_path()."/pdf/";
        $folder = $basepath.date('Ymdhis');
        // 1日前に作成したフォルダを削除
        $one = $basepath."*";
        $glob = glob($one);
        foreach ($glob as $i => $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if($ext != "pdf"){
                if (filemtime($file) < time() - 3600) {
                    //echo '<p>' . $file . 'は、1時間以上のファイルです</p>';
                    if(is_dir($file)){
                        $this->rmdir_recursively($file);
                    }else{
                        unlink($file);
                    }
                } else {
                    //echo '<p>' . $file . 'は、1時間以内のファイルです</p>';
                }
            }
        }

        //if(file_exists($folder) == false){
        @mkdir($folder, 0777);
        //}
        $filepath = storage_path('app')."/";
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $targetCount = 0;
        foreach($data as $key=>$value){
            //フォルダのコピー
            $proceeding = $value->proceeding;
            $flash = $value->flash;
            $poster = $value->poster;
            $path = "";
            if(
                ($flag == 1 && $value->proceeding_flag == 1 )
                ||
                ($value->disp_status1 && $flag == 0)
                ){
                if($proceeding) $path = $proceeding;
                $dir = $filepath.dirname($path)."/";
                $basename = basename($path);
                if(pathinfo($basename, PATHINFO_FILENAME)){
                    @copy($dir.$basename,$folder."/".$basename);
                    $targetCount++;
                }
            //    $this->copy_dir($dir,$folder,$path);
            }
            if(
                ($flag == 1 && $value->flash_flag == 1 )
                ||
                ($value->disp_status2 && $flag == 0)){
                if($flash) $path = $flash;
                $dir = $filepath.dirname($path)."/";
                $basename = basename($path);
                if(pathinfo($basename, PATHINFO_FILENAME)){
                    @copy($dir.$basename,$folder."/".$basename);
                    $targetCount++;
                }
            //    $this->copy_dir($dir,$folder,$path);
            }
            if(
                ($flag == 1 && $value->poster_flag == 1 )
                ||
                ($value->disp_status3 && $flag == 0) ){
                if($poster) $path = $poster;
                $dir = $filepath.dirname($path)."/";
                $basename = basename($path);
                if(pathinfo($basename, PATHINFO_FILENAME)){
                    @copy($dir.$basename,$folder."/".$basename);
                    $targetCount++;

                }
            //    $this->copy_dir($dir,$folder,$path);
            }
        }
        if(!$targetCount){
            // return redirect()->back()->with('message', '対象のファイルが存在しないため、ダウンロードに失敗しました。');
            echo "対象のファイルが存在しないため、ダウンロードに失敗しました。";
            exit();
        }
        $fileName = "zipFile1".date('YmdHis');
        $command =  "cd ". $folder .";"."zip -r ". $basepath . $fileName .".zip .";
        if(env('DB_HOST') == 'localhost'){
            echo $command;
            exit();
        }

        exec($command);

        mb_http_output( "pass" );
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");

        header('Content-Disposition: attachment; filename*=UTF-8\'\'' . $fileName.".zip");
        ob_end_clean();
        readfile($basepath.$fileName.".zip");
        exit();
    }

    public function rmdir_recursively($dir) {
        $dh = opendir($dir);
        if ($dh === false) {
            throw new Exception("Failed to open $dir");
        }

        while (true) {
            $file = readdir($dh);
            if ($file === false) {
                break;
            }
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = rtrim($dir, '/') . '/' . $file;
            if (is_dir($path)) {
                rmdir_recursively($path);
            } else {
                unlink($path);
            }
        }
        closedir($dh);
        rmdir($dir);
    }


    public function copy_dir($dir, $new_dir, $path)
    {
        $dir     = rtrim($dir, '/').'/';
        $new_dir = rtrim($new_dir, '/').'/';

        // コピー元ディレクトリが存在すればコピーを行う
        if (is_dir($dir)) {
            // コピー先ディレクトリが存在しなければ作成する
            if (!is_dir($new_dir)) {
                mkdir($new_dir,0777);
                chmod($new_dir,0777);
            }

            // ディレクトリを開く
            if ($handle = opendir($dir)) {
                // ディレクトリ内のファイルを取得する
                while (false !== ($file = readdir($handle))) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    // 下の階層にディレクトリが存在する場合は再帰処理を行う
                    if (is_dir($dir.$file)) {
                    //    $this->copy_dir($dir.$file, $new_dir.$file);
                    } else {
                        if(basename($path) == $file){
                            copy($dir.$file, $new_dir.$file);
                        }
                    }

                }
                closedir($handle);
            }
        }
    }
}
