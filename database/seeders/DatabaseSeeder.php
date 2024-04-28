<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 開発用
        $this->call(UsersTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        // $this->call(PageContentsTableSeeder::class);
        // $this->call(PageSubContentsTableSeeder::class);
        $this->call(MailformsTableSeeder::class);

        // 初期データ登録
        $this->call(PagesTableSeeder::class);   // 公開ページ一覧登録
    }
}
