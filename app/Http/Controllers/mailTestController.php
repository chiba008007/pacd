<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class mailTestController extends Controller
{
    //
    public function index(){

        for($i=1;$i<=2;$i++){
            echo "mail";

            //言語と文字コードを設定
            mb_language("Japanese");
            mb_internal_encoding("UTF-8");

            //メールの情報を設定
            $mailto = "chiba@se-sendai.co.jp";
            $title = "タイトルテスト".$i;
            $message = "本文のテストです。";
            $admin = config("admin.email");
            $head = config("admin.head");
            $header = "From: " .mb_encode_mimeheader($head) ."<".$admin.">";
            //$header .= "\n";
            //$header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
            $pfrom = "-f$admin";
            //メールの送信
            if(mb_send_mail($mailto,$title,$message,$header,$pfrom)){
                echo "送信成功".$i;
            }else{
                echo "送信失敗".$i;
            }
        }

        /*
                mb_language("Japanese");
                mb_internal_encoding("UTF-8");


                $admin = config("admin.email");
                $head = config("admin.head");

                $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
                $header.="\n";
            //    $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
            //    $header.="\n";
                $body = "aaaaaaaaaa";
                $this->to = "chiba@innovation-gate.jp";
                $this->title = "test";

                if(mb_send_mail($this->to , $this->title , $body,$header)){
                    echo "OK";
                }else{
                    echo "NG";
                }
                */

        /*
        $body = "test";
        $this->to = "chiba@innovation-gate.jp";
        $this->title = "test";

        Mail::raw($body, function ($message) {
            $message->to($this->to)
                ->subject($this->title);
        });
        */

        exit();
    }


}
