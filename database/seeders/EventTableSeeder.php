<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class EventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $event = [
                'category_type' => "1",
                'code' => "sample1234",
                'name' => "（公社）日本分析化学会 高分子分析研究懇談会",
                'date_start'=>"2020-02-01",
                'date_end'=>"2020-02-20",
                'place'=>'webex meetingsによるweb開催（会員に別途案内）',
            ];
            DB::table('events')->insert($event);
        } catch (Exception $e) {}
    }
}
