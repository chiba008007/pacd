<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class insertpages extends Seeder
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
            'title' => "企業協賛",
            'uri' => "/kyosan",
            'route_name' => "kyosan",
            'is_form' => "0",
            'is_opened' => "1",

        ];
        DB::table('pages')->insert($pages);
    }
}
