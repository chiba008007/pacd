<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i<=4; $i++) {
            try {
                $user = [
                    'login_id' => "user$i",
                    'email' => "user$i@sample.com",
                    'password' => Hash::make('password'),
                    'sei' => 'テスト',
                    'mei' => "ユーザー$i",
                    'sei_kana' => 'てすと',
                    'mei_kana' => 'ゆーざー',
                    'remarks' => 'テストユーザー',
                    'type' => $i,
                ];
                DB::table('users')->insert($user);
            } catch (Exception $e) {}
        }
    }
}
