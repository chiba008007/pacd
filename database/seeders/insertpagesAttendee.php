<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class insertpagesAttendee extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $pages = [
            'title' => "企業協賛申込",
            'uri' => "/kyosan/{event_id}/attendee",
            'route_name' => "kyosan_attendee",
            'is_form' => "1",
            'is_opened' => "1",

        ];
        DB::table('pages')->insert($pages);
    }
}
