<?php

namespace Database\Seeders;

use App\Models\FormInput;
use Exception;
use Illuminate\Database\Seeder;

class FormInputsCsvOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 技術講習会の振込予定日、参加申込する会員区別の会員番号をCSV出力
        try {
            FormInput::find(11)->update(['csvflag' => 1]);
            FormInput::find(14)->update(['csvflag' => 1, 'csvtag' => 21]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
