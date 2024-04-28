<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\Event_join;
use App\Models\Yearpayment;
use App\Models\Pdfstorage;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{
    //会員登録用
    //uid,typeは管理画面からのアクセス
    public function index($id, $uid = "", $type = "")
    {
        //ダウンロード
        if ($uid) {
            $user = User::where(['id' => $uid])->first();
        } else {
            $user = Auth::user();
        }
        $uid = $user->id;
        $usertype = $user->type;
        $username = $user->sei . " " . $user->mei;

        $payment = Payment::where(['id' => $id, "uid" => $uid])->first();
        $paymentall = Payment::where(["uid" => $uid, "status" => 0])
            ->where('years', '>=', $payment['years'] - 2)
            ->where('years', '<=', $payment['years']);

        $paymentall_recipe = Payment::where(["uid" => $uid, "recipe_status" => 0])
            ->where('years', '>=', $payment['years'] - 2)
            ->where('years', '<=', $payment['years']);
        $yearpaymentdata = Yearpayment::where(['year' => $payment->years])->first();

        //管理画面からの遷移の為無理矢理ステータスを変更する
        if ($type == "invoice") $payment->status = 0;
        if ($type == "recipe") $payment->status = 1;

        $category  = config('pacd.categorykey')[$payment->type]['payments'];

        $set = [];

        $set['bank_name'] = ($yearpaymentdata->bank_name) ?? config('pacd.bank.name');
        $set['bank_code'] = ($yearpaymentdata->bank_code) ?? config('pacd.bank.code');
        $set['invoice_address'] = ($yearpaymentdata->invoice_address) ?? config('pacd.bank.invoice_address');
        $set['invoice_memo'] = ($yearpaymentdata->invoice_memo) ?? config('pacd.bank.invoice_memo');
        $set['recipe_memo'] = ($yearpaymentdata->recipe_memo) ?? config('pacd.bank.recipe_memo');

        $price = config('pacd.user.yearPrice')[$usertype];;
        $set['pay'] = number_format($price);
        $set['name'] = $username;
        $set['category'] = $category;
        $set['usertype'] = $usertype;
        $set['cp_name'] = $user->cp_name;
        $set['busyo'] = $user->busyo;
        //未払いの合算を表示
        $set['price'] = number_format($price * $paymentall->count());
        $set['priceTax'] = number_format(ceil($price * 10 / 110));
        $set['price_recipe'] = number_format($price * $paymentall_recipe->count());
        $set['date'] = date("Y年m月d日");
        $date = date("Y/m/d H:i:s");
        $set['yearall'] = $paymentall->get();
        $set['yearall_recipe'] = $paymentall_recipe->get();
        $num = sprintf($user->type_number);
        $set['num'] = $num;
        $set['join_name'] = "年会";
        //0のときは請求書
        if ($payment->status == 0) {
            //ステータス変更 管理画面からの遷移では無いとき
            if (!$type) Payment::where('id', $id)->where('uid', $uid)->update(['invoice_status' => 1, 'invoice_date' => $date]);
            //請求金額
            $pdf = PDF::loadView('invoice', $set)->setPaper('a4', '');
        } else {
            //領収書
            //ステータス変更
            if (!$type) Payment::where('id', $id)->where('uid', $uid)->update(['recipe_status' => 1, 'recipe_date' => $date]);
            //領収書金額
            $pdf = PDF::loadView('recipe', $set)->setPaper('a4', 'landscape');
        }
        $already = Pdfstorage::where(
            [
            'user_id'=>$user->id,
            'event_id'=>0, // 0の時は年会費
            'type'=>$payment->status
            ])->count();
        $exists = $already > 0 ? "_再発行":"";

        // PDFをローカルに保存する
        $pdfpath = storage_path('pdf');
        $filecompanyname = mb_substr($user->cp_name."・".$user->busyo,0,15,'UTF-8');
        $file = "年会費_".date('ymdHis')."_".$user->login_id."_".$filecompanyname."_".$price.$exists;
        $pdf->save($pdfpath  ."/original/". $file.".pdf");
        // 検索用
        $filenamecode = date('ymdHis')."_".$user->login_id;
        $pdf->save($pdfpath  ."/search/". $filenamecode.".pdf");
        
        // ローカルに保存したデータをDBに保持する
        $storageSet = [
            "user_id"=>$user->id,
            "event_id"=>0,
            "filename"=>$file,
            "filenamecode"=>$filenamecode,
            "type"=>$payment->status,
            "create_date"=>date('Y-m-d H:i:s'),
        ];
        Pdfstorage::create($storageSet);

        //ダウンロード
        //$date = "recipe_".date("Ymdhis");
        // return $pdf->download('hello.pdf');
        // return $pdf->download($date.'.pdf');
        //ブラウザ上に出力
        return $pdf->stream();
    }

    //参加者用
    //$idはカテゴリータイプ
    //$uid,codeは管理画面から
    public function join($id, $type, $uid = "", $code = "")
    {

        if ($uid) {
            $user = User::where(['id' => $uid])->first();
        } else {
            $user = Auth::user();
        }
        $uid = $user->id;
        $attend = Attendee::where([
            'attendees.id' => $id,
            'attendees.user_id' => $uid
        ])
            ->select([
                'event_id',
                'event_number',
                'is_paid',
                'event_join_id_list',
            ])
            //->leftjoin('event_joins','event_joins.id','=','attendees.event_join_id')
            ->first();

        $event = Event::where("id", $attend->event_id)->first();

        $exp = [];
        if ($attend->event_join_id_list) {
            $exp = explode(",", $attend->event_join_id_list);
        }

        // 分割の時はevent_joinを講演会と懇親会に分ける
        $event_join2 = [];
        if($event->outputtype === 2){
            $event_join = Event_join::whereIn('id', $exp)->where('pattern',1)->get();
            $event_join2 = Event_join::whereIn('id', $exp)->where('pattern',2)->get();
        }else{
            $event_join = Event_join::whereIn('id', $exp)->get();   
        }
        // 消費税の計算
        foreach ($event_join as $key => $value) {
            $event_join[$key]['join_price'] = $value->join_price;
            $event_join[$key]['join_price_noTax'] = $value->join_price - ($value->join_price / 1.1 * 0.1);
            $event_join[$key]['join_price_tax'] = $value->join_price / 1.1 * 0.1;
        }
        foreach ($event_join2 as $key => $value) {
            $event_join2[$key]['join_price'] = $value->join_price;
            $event_join2[$key]['join_price_noTax'] = $value->join_price - ($value->join_price / 1.1 * 0.1);
            $event_join2[$key]['join_price_tax'] = $value->join_price / 1.1 * 0.1;
        }

        //管理画面からの遷移の為無理矢理ステータスを変更する
        if ($code == "invoice") $attend->is_paid = 0;
        if ($code == "recipe") $attend->is_paid = 1;
        $date = date("Y/m/d H:i:s");

        //請求金額
        $price = 0;
        $price2 = 0;
        foreach ($event_join as $key => $value) {
            $price += $value->join_price;
        }
        foreach ($event_join2 as $key => $value) {
            $price2 += $value->join_price;
        }

        // 消費税前
        // $price = $price -$price*0.1;
        $set['category_type'] = $event->category_type;
        $set['name'         ] = $user->sei . " " . $user->mei;
        $set['category'     ] = config('pacd.categorykey.' . $type . ".name") . "参加費および懇親会費として";
        $set['price'        ] = number_format($price);
        $set['priceTax'     ] = number_format(floor(($price) * 10 / 110));
        $set['priceNoTax'   ] = number_format($price - floor(($price) * 10 / 110));
        $set['price2'       ] = number_format($price2);
        $set['priceTax2'    ] = number_format(floor(($price2) * 10 / 110));
        $set['priceNoTax2'  ] = number_format($price2 - floor(($price2) * 10 / 110));
        $set['date'         ] = date("Y年m月d日");
        $set['join_name'    ] = $attend->join_name;
        $set['usertype'     ] = $user->type;
        $set['cp_name'      ] = $user->cp_name;
        $set['busyo'        ] = $user->busyo;
        $set['bank_name'    ] = ($event->bank_name) ?? config('pacd.bank.name');
        $set['bank_code'    ] = ($event->bank_code) ?? config('pacd.bank.code');
        $set['invoice_address'] = ($event->invoice_address) ?? config('pacd.bank.invoice_address');
        $set['invoice_memo'   ] = ($event->invoice_memo) ?? config('pacd.bank.invoice_memo');
        $set['recipe_memo'    ] = ($event->recipe_memo) ?? config('pacd.bank.recipe_memo');
        $set['event_join'     ] = $event_join;
        $set['event_join2'    ] = $event_join2;
        $set['outputtype'    ] = $event->outputtype;
        if (isset($event->name)) {
            $set['event_name'] = $event->name;
        } else {
            $set['event_name'] = "";
        }
        //作成された日付より年を取得する
        $create_ts = explode("-", $user->created_at);
        $set['year'] = $create_ts[0];
        $num = sprintf($user->type_number);
        $set['num'] = $num;
        $set['event_number'] = sprintf("%010d", $attend->event_number);
        if ($attend->is_paid == 0) {
            if (!$code) Attendee::where('id', $id)->where('user_id', $uid)->update(['invoice_status' => 1, 'invoice_date' => $date]);
            $pdf = PDF::loadView('invoice_join', $set)->setPaper('a4', '');

        } else {
            //領収書
            if (!$code)  Attendee::where('id', $id)->where('user_id', $uid)->update(['recipe_status' => 1, 'recipe_date' => $date]);
            $pdf = PDF::loadView('recipe_join', $set)->setPaper('a4', 'landscape');
        }

        // すでにローカルに保持してあるかの確認
        $already = Pdfstorage::where(
            [
            'user_id'=>$user->id,
            'event_id'=>$attend->event_id,
            'type'=>$attend->is_paid
            ])->count();
        $exists = $already > 0 ? "_再発行":"";


        // PDFをローカルに保存する
        $pdfpath = storage_path('pdf');
        // ファイル名の例
        //24年会費(イベント名）_240301(発行日・DL日）_PL2A19002(会員番号)_農業・食品産業技術総合研究機構(法人名・所属名　15文字くらいまで）_2000(金額）.pdf

        $filecompanyname = mb_substr($user->cp_name."・".$user->busyo,0,15,'UTF-8');
        $file = $event->name."_".date('ymdHis')."_".$attend->event_number."_".$user->login_id."_".$filecompanyname."_".$price.$exists;
        $pdf->save($pdfpath  ."/original/". $file.".pdf");

        // 検索用
        $filenamecode = date('ymdHis')."_".$attend->event_number."_".$user->login_id;
        $pdf->save($pdfpath  ."/search/". $filenamecode.".pdf");
        
        // ローカルに保存したデータをDBに保持する
        $storageSet = [
            "user_id"=>$user->id,
            "event_id"=>$attend->event_id,
            "filename"=>$file,
            "filenamecode"=>$filenamecode,
            "type"=>$attend->is_paid,
            "create_date"=>date('Y-m-d H:i:s'),
        ];
        Pdfstorage::create($storageSet);
       return redirect()->route('member.join.dispalyPdf',['filecode' => $filenamecode]);
    }

    public function dispalyPdf($filecode){


        $filename = storage_path('pdf/search/'.$filecode.".pdf");
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');
        //header('Content-Length: ' . filesize($filename));
        readfile($filename);
        exit();
    }
}
