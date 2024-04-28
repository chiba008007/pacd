<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\URL;
use App\Models\Attendee;

class QrHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $set['title'] = 'QRDashboard';
        //イベント情報の取得
        // $date = date("Y-m-d");
        // $event = Event::orderby("id","desc")->where('status',1)->where("date_end",">=",$date)->get();
        // $list = [];
        // foreach($event as $key=>$value){
        //     $list[$value->category_type][] = $value;
        // }

        // $set['event'] = $list;
        return view('admin.qrhome', $set);
    }
    public function joinstatus($id,$attend_id,$event_id,$user_id){
        $msg = "";
        $error = "";
        $set = [];

        $flg = Attendee::where('id',$attend_id)->where('event_id',$event_id)->where('user_id',$user_id)->update(['join_status'=>1]);
        if($flg == 1){
            $msg = "受付に成功しました";
        }else{
            $error = "受付に失敗しました";
        }
        /*
        if(Attendee::where('id',$attend_id)->where('event_id',$event_id)->where('user_id',$user_id)->update(['join_status'=>1])){
            $msg = "受付に成功しました";
        }else{
            $error = "受付に失敗しました";
        }

        $set['title'] = 'QRDashboard';
        $set[ 'message' ] = $msg;
        $set[ 'error'   ] = $error;
        */
        $set[ 'message' ] = $msg;
        $set[ 'error'   ] = $error;
        return view('admin.joinStatusResult', $set);
    }
}
