<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $admin = [
                'login_id' => 'admin',
                'email' => 'admin@sample.com',
                'password' => Hash::make('password'),
            ];
            DB::table('admins')->insert($admin);
        } catch (Exception $e) {}

        // 利用不可管理者ユーザー
        try {
            $admin = [
                'login_id' => 'admin0',
                'email' => 'admin0@sample.com',
                'status' => 0,
                'password' => Hash::make('password'),
            ];
            DB::table('admins')->insert($admin);
        } catch (Exception $e) {}
    }
}
