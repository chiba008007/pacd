<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class PageSubContentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 委員会サンプル
        $iinkai = [
            [
                2,
                '香川 信之',
                '東ソー分析センター'
            ],
            [
                3,
                '中谷 善昌 (例会)',
                '資生堂'
            ],
            [
                3,
                '百瀬 陽（講習会）',
                '三菱ケミカル',
            ]
        ];

        foreach ($iinkai as $content) {
            try {
                $data = [
                    'page_content_id' => $content[0],
                    'content1' => $content[1],
                    'content2' => $content[2],
                ];
                DB::table('page_sub_contents')->insert($data);
            } catch (Exception $e) {}
        }

        // 例会サンプル
        $reikai = [
            [
                5,
                'イベント名',
                '（公社）日本分析化学会 高分子分析研究懇談会'
            ],
            [
                5,
                '日時',
                '2021年1月27日（水）13時00分 ～ 15時00分'
            ],
            [
                5,
                '場所',
                'webex meetingsによるweb開催（会員に別途案内）'
            ],
            [
                5,
                '',
                '会員限定です。無料となります。'
            ],
        ];
        foreach ($reikai as $content) {
            try {
                $data = [
                    'page_content_id' => $content[0],
                    'content1' => $content[1],
                    'content2' => $content[2],
                ];
                DB::table('page_sub_contents')->insert($data);
            } catch (Exception $e) {}
        }

        // 個人情報サンプル
        $privacy = [
            [
                8,
                '高分子分析研究懇談会は日本分析化学会のプライバシーポリシーに基づき個人情報の収集・利用・管理を行います。',
                ''
            ],
            [
                8,
                '高分子分析討論会のオンライン登録のページにおいて収集した個人情報については、高分子分析討論会および本研究懇談会の事業の連絡のためにのみ使用し、その他の目的には使用しません。',
                ''
            ],
        ];

        foreach ($privacy as $content) {
            try {
                $data = [
                    'page_content_id' => $content[0],
                    'content1' => $content[1],
                    'content2' => $content[2],
                ];
                DB::table('page_sub_contents')->insert($data);
            } catch (Exception $e) {}
        }
    }
}
