<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Library\PagesLibrary;
use App\Models\Attendee;
use App\Models\Page;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Event_join;
use Illuminate\Support\Facades\DB;



// 公開ページ管理
class PagesController extends Controller
{
    public function page(){
        $type = "";
        $title = "";
        if(Route::currentRouteName() == "reikai.page"){
            $type = 2;
            $title = "例会&講演会";
        }
        if(Route::currentRouteName() == "touronkai.page"){
            $type = 3;
            $title = "高分子分析討論会";
        }
        if(Route::currentRouteName() == "kosyukai.page"){
            $type = 4;
            $title = "高分子分析技術講習会";
        }
        //例会の最新の一見を取得
        $mostnew = Event::getEventMostNew();

        //参加料金
        $eventsjoin = Event_join::getEventJoin();
        $set['eventsjoin'] = $eventsjoin;


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

        $set['event'] = $mostnew[$type];
        $set['title'] = $title;
        $set['route_name'] = "";
        $set['contents'] = "";
        return view('eventpages', $set);
    }
    public function view()
    {
        $user = Auth::user();

        $id = 1;
        $bs = DB::table('banner_setting')->where('id',$id)->first();
        $now = date("Y-m-d");
        $bannar = DB::table('banner')
            ->where( 'status',1 )
            ->where( 'startdate',"<=",$now )
            ->where( 'enddate',">=",$now )
            ->orderBy('sort')->get();

        // DBに登録されているページコンテンツ内容を取得
        $set = PagesLibrary::getContents(['route_name' => Route::currentRouteName()]);
        //各イベントの最新のIDを取得
        $first_event = Event::getEventFirst();
        $set['firstEvent'] = $first_event;
        $set['smooth'] = ($bs->smooth*1000)??3000;
        $set[ 'bannar' ] = $bannar;

        $coworkCount = 0;
        if($user){
            // 協賛企業への登録有無の確認
            $cowork = Attendee::select(['attendees.*','events.join_enable','events.category_type'])
            ->join('events', 'events.id', '=', 'attendees.event_id')
            ->where([
                'user_id' => $user->id,
                'events.category_type' => config('pacd.category.kyosan.key'),
                'events.status'=>1
                ])
            ->get();
            $coworkCount = $cowork->count();
        }

        $set['kyosanCount'] = $coworkCount;
        return view('pages', $set);
    }

    // ※ 参加者登録、講演者登録ページは Reikai|Touronkai|Kosyukai\Attendee\Register::createで表示
        public function paymentSuccess()
    {
        $set['title'] = 'お支払い完了';
        return view('pages.payment_success', $set);
    }
}
