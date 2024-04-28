<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            [
                'title' => '高分子分析研究懇談会',
                'route_name' => 'top',
                'uri' => '/',
                'description' => '高分子の分析に特化した学会です。',
            ],
            [
                'title' => '高分子分析研究懇談会について',
                'route_name' => 'concept',
                'uri' => '/concept',
            ],
            [
                'title' => '開催行事一覧',
                'route_name' => 'schedule',
                'uri' => '/schedule',
    
            ],
            [
                'title' => '例会＆講演会',
                'route_name' => 'reikai',
                'uri' => '/reikai',
            ],
            [
                'title' => '過去の例会一覧',
                'route_name' => 'reikai.history',
                'uri' => '/reikai/history',
            ],
            [
                'title' => '高分子分析討論会',
                'route_name' => 'touronkai',
                'uri' => '/touronkai',
            ],
            [
                'title' => '過去の討論会一覧',
                'route_name' => 'touronkai.history',
                'uri' => '/touronkai/history',
            ],
            [
                'title' => '高分子分析技術講習会',
                'route_name' => 'kosyukai',
                'uri' => '/koshikai',
            ],
            [
                'title' => '過去の講習会一覧',
                'route_name' => 'kosyukai.history',
                'uri' => '/kosyukai/history',
            ],
            [
                'title' => '高分子分析ハンドブック',
                'route_name' => 'handbook',
                'uri' => '/handbook',
            ],
            [
                'title' => '入会案内',
                'route_name' => 'nyukai',
                'uri' => '/nyukai',
            ],
            [
                'title' => '運営委員会・企画委員会',
                'route_name' => 'iinkai',
                'uri' => '/iinkai',
            ],
            [
                'title' => 'お問い合わせ',
                'route_name' => 'contact',
                'uri' => '/contact',
            ],
            [
                'title' => 'リンク集',
                'route_name' => 'link',
                'uri' => '/link',
            ],
            [
                'title' => '求人情報',
                'route_name' => 'kyujin',
                'uri' => '/kyujin',
            ],
            [
                'title' => '個人情報について',
                'route_name' => 'privacy',
                'uri' => '/privacy',
            ],
            [
                'title' => '会員登録',
                'route_name' => 'register',
                'uri' => '/register',
                'is_form' => 1
            ],
            [
                'title' => '例会＆講演会参加申込',
                'route_name' => 'reikai_attendee',
                'uri' => '/reikai/{event_id}/attendee',
                'is_form' => 1
            ],
            [
                'title' => '高分子分析討論会参加申込',
                'route_name' => 'touronkai_attendee',
                'uri' => '/touronkai/{event_id}/attendee',
                'is_form' => 1
            ],
            [
                'title' => '高分子分析技術講習会参加申込',
                'route_name' => 'kosyukai_attendee',
                'uri' => '/kosyukai/{event_id}/attendee',
                'is_form' => 1
            ],
            [
                'title' => '例会＆講演会 講演者申込',
                'route_name' => 'reikai_presenter',
                'uri' => '/reikai/attendee/{attendee_id}/presenter',
                'is_form' => 1
            ],
            [
                'title' => '高分子分析討論会 講演者申込',
                'route_name' => 'touronkai_presenter',
                'uri' => '/touronkai/attendee/{attendee_id}/presenter',
                'is_form' => 1
            ],
            [
                'title' => '高分子分析技術講習会 講演者申込',
                'route_name' => 'kosyukai_presenter',
                'uri' => '/kosyukai/attendee/{attendee_id}/presenter',
                'is_form' => 1
            ],
            [
                'title' => 'テストページ',
                'route_name' => 'admin.pages.test',
                'uri' => '/' . config('admin.uri') . '/pages/test',
                'is_opened' => 0,
            ],
        ];

        foreach ($pages as $key => $page) {
            try {
                DB::table('pages')->insert($page);
            } catch (Exception $e) {}
        }
    }
}
