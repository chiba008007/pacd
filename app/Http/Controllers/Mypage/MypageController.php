<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendee;

class MypageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        // 協賛企業への登録有無の確認
        $cowork = Attendee::select(['attendees.*','events.join_enable','events.category_type'])
        ->join('events', 'events.id', '=', 'attendees.event_id')
        ->where([
            'user_id' => $user->id,
            'events.category_type' => config('pacd.category.kyosan.key'),
            'events.status'=>1
            ])
        ->get();
        $set = [];
        $set['kyosanCount'] = $cowork->count();
        return view('mypage.index',$set);
    }
}
