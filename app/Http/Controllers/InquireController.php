<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Models\InquireSetting;
use Illuminate\Support\Facades\Log;


class InquireController extends Controller
{
    //
    public function index(){

        $inquireSetting = InquireSetting::get();
        $set = [];
        $set[ 'title' ] = "問合せフォーム";
        $set[ 'inquireSetting' ] = $inquireSetting;
        return view('inquire', $set);
    }
    public function send(request $request){

       // var_dump($request);

       $validator = $request->validate(
        [
            'name' => 'required'
            ,'kana' => 'required'
            ,'note' => 'required'
            ,'mail' => 'required | email'
            ,'mailconf' => 'required | same:mail'
        ],
        [
            'name.required' => '名前の入力は必須です。',
            'kana.required' => 'ふりがなの入力は必須です。',
            'mail.required' => 'メールアドレスの入力は必須です。',
            'mail.email' => 'メールアドレスに誤りがあります。',
            'mailconf.required' => '確認用メールアドレスの入力は必須です。',
            'mailconf.same' => '確認用メールアドレスに相違があります。',
            'note.required' => 'お問い合わせ内容の入力は必須です。',
        ]);

        $secret = "6LcMjrUdAAAAADzttj0TEF47SCn3op21ey08Z2nv";
        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$_POST["recaptchaResponse"]);
        $reCAPTCHA = json_decode($verifyResponse);
        //テストではtrueを追記
       // if (true || $reCAPTCHA->success){
        if ($reCAPTCHA->success){
            $this->name = $request[ 'name' ];
            $this->mail = $request[ 'mail' ];
            //メール送信
            $data = [];
            $data['name'   ] = $this->name;
            $data['kana'   ] = $request->kana;
            $data['company'] = $request->company;
            $data['tel'    ] = $request->tel;
            $data['mail'   ] = $request->mail;
            $data['number' ] = $request->number;
            $data['note' ] = $request->note;

            $this->to  =  $request->mail;
            $this->bcc = $request[ 'from' ];
            /*
            Mail::send('mails.inquire.index', $data, function($message){
                $message->to($this->mail, $this->name)
                ->from($this->bcc,$this->bcc)
                ->bcc($this->bcc)
                ->subject('お問い合わせメールを送信いたしました。');
            });
            */

            mb_language("Japanese");
            mb_internal_encoding("UTF-8");

            $admin = $this->bcc;
            $head = $this->bcc;

            $body =
$data['name']."様

お世話になっております。
お問い合わせありがとうございました。

以下の内容でお問い合わせを受け付けいたしました。
近日中に、担当者 よりご連絡いたしますので
今しばらくお待ちくださいませ。

━━━━━━□■□　お問い合わせ内容　□■□━━━━━━

お名前:".$data['name']."
ふりがな:".$data['kana']."
所属・勤務先:".$data['company']."
連絡先電話番号:".$data['tel']."
メールアドレス:".$data['mail']."
会員番号:".$data['number']."
お問い合わせ内容
".$data['note']."";


            $header="From: " .mb_encode_mimeheader($head) ."<".$admin.">";
            $header.="\n";
           // $header.="Bcc:" .mb_encode_mimeheader("管理者") ."<".$admin.">";
           // $header.="\n";
            $pfrom = "-f$admin";

            mb_send_mail($this->to , 'お問い合わせメールを送信いたしました。' , $body,$header,$pfrom);

            mb_send_mail($admin , '[管理者向け]お問い合わせメールを送信いたしました。' , $body,$header,$pfrom);

            $text = '';
            foreach($data as $key=>$value){
                $text .= $key."=>".$value."\n";
            }
            Log::info("お問い合わせメール配信 to:".$this->mail."\n".$text);

            return redirect(route('inquire'))->with('flash_message', '送信が完了しました');
        }else{
            echo "配信エラー";
            exit();
        }
    }
}
