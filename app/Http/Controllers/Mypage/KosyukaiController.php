<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class KosyukaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $set['title'] = config('pacd.category.kosyukai.name') . ' マイページ';
        $set['attendees'] = Attendee::select(['attendees.*','events.join_enable'])
            ->orderBy('events.id',"DESC")
            ->join('events', 'events.id', '=', 'attendees.event_id')
            ->where([
                'user_id' => $user->id,
                'events.category_type' => config('pacd.category.kosyukai.key'),
                'events.status'=>1
            ])->get();
        $set['user'] = $user;

        $id = 1;
        $bs = DB::table('banner_setting')->where('id',$id)->first();
        $now = date("Y-m-d");
        $bannar = DB::table('banner')
            ->where( 'status',1 )
            ->where( 'startdate',"<=",$now )
            ->where( 'enddate',">=",$now )
            ->orderBy('sort')->get();
        $set['smooth'] = ($bs->smooth*1000)??3000;
        $set[ 'bannar' ] = $bannar;

        return view('mypage.kosyukai.index', $set);
    }
}
