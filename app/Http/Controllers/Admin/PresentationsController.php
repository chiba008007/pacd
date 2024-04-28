<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use Illuminate\Http\Request;

class PresentationsController extends Controller
{
    // 講演内容を配列で返す(POST)
    public function get($presentation_id)
    {
        $presentations = Presentation::find($presentation_id);
        return $presentations ? $presentations->toArray() : false;
    }

    // 発表番号を配列で返す(POST)
    public function get_numbers($event_id = "")
    {
        $query = Presentation::select('presentations.id','presentations.number')
                    ->leftJoin('presenters', 'presenters.id', '=', 'presentations.presenter_id')
                    ->leftJoin('attendees', 'attendees.id', '=', 'presenters.attendee_id');
        if ($event_id) {
            $query->where('attendees.event_id', $event_id);
        }
        return $query->get()->toArray();
    }
}
