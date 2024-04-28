<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * webexページ表示
     *
     * @param int $id ... event_id
     * @return view
     */
    public function index($id,$date="")
    {
        $event = Event::where('code', $id)->first();
        // 登録されていないイベントコードの場合エラー
        if (!$event) {
            abort(404);
        }
        // 支払い済みの参加者でない場合エラー
        $attendee = $event->attendees->where('user_id', Auth::id())->first();
        /*
        if (!Auth::guard('admin')->check() && !$attendee || !$attendee->is_paid) {
            if (!$attendee->is_paid) {
                return redirect()->back()->with('message', '参加費の支払いが完了していないためページを開けませんでした。');
            }
            abort(404);
        }
        */

        $set['event_id'] = $event->id;
        $set['title'] = $event->name;
        //イベントの日付取得
        $set['dateTerm'] = $event->getEventDataCodeDateTerm($id);

        //programデータ取得
        $dates = ($date)?$date:$set['dateTerm'][0];
        $program = Program::getProgram($event->event_id,$dates);
        $set['program'] = $program;
        $set['category_type'] = $event->category_type;

        if ($event->category_type === 4){
            $category = 'doc_dl';
        }else{
            $category = 'is_paid';
        }
        $set['category'] = $category;

        //現在の発表情報を取得
        $set['now'] = Program::getProgramNow($event->event_id,$dates);
        $set['id'] = $id;
        $set['dates'] = $dates;
        $set['attendee'] = $attendee;

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


        $sql = "
                SELECT
                    p.type,
                    (SUM( CASE WHEN pre.proceeding IS NULL THEN 0 ELSE 1 END ) +
                    SUM( CASE WHEN pre.flash IS NULL THEN 0 ELSE 1 END ) +
                    SUM( CASE WHEN pre.poster IS NULL THEN 0 ELSE 1 END ) ) as count
                FROM
                    programs as p
                    LEFT JOIN program_lists as pl ON p.id = pl.program_id
                    LEFT JOIN presentations as pre ON pre.id = pl.presentation_id
                where
                    p.event_id = ? AND
                    p.date = ?
                group by pre.id, p.type HAVING count > 0
        ";

        $data = DB::select($sql,[$event->id,$dates]);
        $buttonFlag = [];
        foreach($data as $key=>$value){
            $buttonFlag[$value->type] = $value->count;
        }
        $set[ 'buttonFlag' ] = $buttonFlag;
        return view('webex.index',$set);
    }
}
