<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaperOutputController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request, $id)
    {

        $event = Event::where('code', $id)->first();

        $attendee = $event->attendees->where('user_id', Auth::id())->first();
        $user = User::where('id', $attendee->user_id)->first();
        $url = url('/') . "/" . config("admin.uri") . "/event/" . $id . "/joinstatus/" . $attendee->id . "/" . $event->id . "/" . $user->id;
        $set['event_number'] = sprintf("%010d", $attendee->event_number);
        $set['event'] = $event;
        $set['user'] = $user;
        $set['url'] = $url;
        $set['attendee'] = $attendee;
        $set['ispaid'] = config('pacd.payment')[$attendee->is_paid];
        $pdf = PDF::loadView('paperoutput', $set)->setPaper('a4', '');
        return $pdf->stream();
    }
}
