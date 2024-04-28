<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class PageContentsTableSeeder extends Seeder
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
                1,
                '',
                '2020年度運営委員会・企画委員会',
                'text'
            ],
            [
                2,
                '運営委員長',
                '',
                'list'
            ],
            [
                3,
                '副委員長',
                '',
                'list',
            ]
        ];

        foreach ($iinkai as $content) {
            try {
                $data = [
                    'id' => $content[0],
                    'page_id' => 12,
                    'title' => $content[1],
                    'content' => $content[2],
                    'content_type' => $content[3]
                ];
                DB::table('page_contents')->insert($data);
            } catch (Exception $e) {}
        }

        // 例会サンプル
        $reikai = [
            [
                4,
                '第403回例会開催のご案内',
                '時下ますますご清栄のこととお慶び申し上げます。第403回例会を下記の内容にて開催致します。奮ってご参加ください。',
                'text'
            ],
            [
                5,
                '記',
                '',
                'table'
            ],
            [
                6,
                '講演内容',
                '<h4>2021年1月27日<br>
                受付（13:00 ～ 13:25）</h4>
                <h4>開会のあいさつ (13:25 ～ 13:30)<br>
                （東ソー分析センター）香川　信之</h4>
                <h4 style="text-align : left;" align="left"><strong>講演 (13:30 ～ 14:30)</strong><br>
                「複雑流体流動挙動の階層性を誘発する溶液内部の不均一さ」<br>
                （神戸大学）日出間　るり</h4>
                <p>　高分子や紐状に会合する界面活性剤など，ソフトマターを含む溶液は，低濃度であっても複雑な流動挙動を示すため，複雑流体と呼ばれている．ある条件で急に物性が変化したり，観察する階層（長さスケール）によって流動の安定・不安定が変わったりといった複雑流体特有の現象は，溶液内部の不均一な構造に由来すると考えられる．本講演では，高分子溶液がメートルスケールで乱流を抑制する現象，マイクロメートルスケールで乱れを誘発する現象を示し，これらに影響を与える溶液内部の不均一さ定量化について紹介する．</p>
                <h4 style="text-align : left;" align="left"><strong>休憩 (14:30 ～ 14:35)</strong><br>
                <strong><br>
                （予定）講演 (14:35～ 14:55)</strong>　第25回高分子分析討論会　優秀発表賞受賞講演<br>
                「反応熱分解GC-MSによる強固な架橋構造を有する紫外線硬化アクリレート共重合体の組成及び構造解析」 <br>
                （名古屋工業大学大学院工学研究科）加藤章太郎</h4>
                <p> 　水酸化テトラメチルアンモニウム（TMAH）を試料に添加して熱分解する反応熱分解GC-MSは、エステル結合で構成される紫外線硬化樹脂の架橋構造解析に効果的に用いられてきた。しかし、架橋ネットワーク構造が強固な場合には、分解効率が不十分で、十分な定量精度が得られない場合がある。本研究では、こうした紫外線硬化アクリレート共重合体をあらかじめマイクロチューブ内でTMAH溶液中に浸漬して可溶化後、溶液を熱分解GC-MS測定する方法により、共重合組成や硬化反応の進行度を高精度に分析することに成功した。</p>
                <h4 style="text-align : left;" align="left"><strong>閉会のあいさつ（14:55 ～ 15:00）<br>
                （東ソー分析センター）香川　信之</strong></h4>
                <h4>申込方法</h4>
                <p>　会員限定で別途電子メールにてご案内いたします。</p>
                <h4>申込先，問合せ先</h4>
                <p>〒220-8559　神奈川県横浜市西区高島１丁目２－１１<br>
                (株)資生堂　グローバルイノベーションセンター　　中谷　善昌<br>
                [E-mail：pacd-reikai-info＠pacd.jp]</p>',
                'text',
            ]
        ];

        foreach ($reikai as $content) {
            try {
                $data = [
                    'id' => $content[0],
                    'page_id' => 4,
                    'title' => $content[1],
                    'content' => $content[2],
                    'content_type' => $content[3]
                ];
                DB::table('page_contents')->insert($data);
            } catch (Exception $e) {}
        }

        // 個人情報サンプル
        $iinkai = [
            [
                7,
                '',
                '<strong>高分子分析研究懇談会では、個人情報に関する法令およびその他の規範を遵守し、お客様の大切な個人情報の保護に万全を尽くします。</strong>',
                'text'
            ],
            [
                8,
                '個人情報の収集について',
                '',
                'list'
            ],
        ];

        foreach ($iinkai as $content) {
            try {
                $data = [
                    'id' => $content[0],
                    'page_id' => 16,
                    'title' => $content[1],
                    'content' => $content[2],
                    'content_type' => $content[3]
                ];
                DB::table('page_contents')->insert($data);
            } catch (Exception $e) {}
        }
    }
}
