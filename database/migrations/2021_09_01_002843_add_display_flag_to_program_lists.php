<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayFlagToProgramLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_lists', function (Blueprint $table) {
            //
            $table->integer('disp_status1')->nullable()->default(1)->after('note')->comment('資料1ステータス');  //カラム追加
            $table->integer('disp_status2')->nullable()->default(1)->after('disp_status1')->comment('資料2ステータス');  //カラム追加
            $table->integer('disp_status3')->nullable()->default(1)->after('disp_status2')->comment('資料3ステータス');  //カラム追加

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_lists', function (Blueprint $table) {
            //
        });
    }
}
