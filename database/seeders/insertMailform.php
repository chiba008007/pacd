<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class insertMailform extends Seeder
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
            'form_type' => "17",
            'title' => "default",
            'note' => "default",
            'status' => "1",
        ];
        DB::table('mailforms')->insert($pages);
    }
}
