<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class eventPasswords extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        try {
            $event = [
                'eventtype' => "reikai",
                'password' => "reikai",
            ];
            DB::table('event_passwords')->insert($event);
            $event = [
                'eventtype' => "touronkai",
                'password' => "touronkai",
            ];
            DB::table('event_passwords')->insert($event);
            $event = [
                'eventtype' => "kyosan",
                'password' => "kyosan",
            ];
            DB::table('event_passwords')->insert($event);
            $event = [
                'eventtype' => "kosyukai",
                'password' => "kosyukai",
            ];
            DB::table('event_passwords')->insert($event);
            $event = [
                'eventtype' => "members",
                'password' => "members",
            ];
            DB::table('event_passwords')->insert($event);
            $event = [
                'eventtype' => "pages",
                'password' => "pages",
            ];
            DB::table('event_passwords')->insert($event);


        } catch (Exception $e) {}
    }
}
