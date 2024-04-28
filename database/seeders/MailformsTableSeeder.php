<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class MailformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $CONST_MAIL_FORM_TEMP = config('pacd.CONST_MAIL_FORM_TEMP');
        foreach($CONST_MAIL_FORM_TEMP as $key=>$value){
            foreach($value as $k=>$val){
                try {
                    $mailforms = [
                        'form_type' => $val['key'],
                        'status'=>1
                    ];
                    DB::table('mailforms')->insert($mailforms);
                } catch (Exception $e) {
                    echo "error";
                }
            }
        }
    }
}
