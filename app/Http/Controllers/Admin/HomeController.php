<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $set['title'] = 'Dashboard';
        //イベント情報の取得
        $date = date("Y-m-d");
        $event = Event::orderby("id","desc")->where('status',1)->where("date_end",">=",$date)->get();
        $list = [];
        foreach($event as $key=>$value){
            $list[$value->category_type][] = $value;
        }

        $set['event'] = $list;
        return view('admin.home', $set);
    }
}
