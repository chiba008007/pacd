<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventsTable3columns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            //
            $table->integer('attendFlag')->default(1)->nullable()->comment('参加者情報確認表示可否')->after('recipe_memo');
            $table->integer('speakerFlag')->default(1)->nullable()->comment('講演申し込み表示可否')->after('attendFlag');
            $table->integer('speakerMenuFlag')->default(1)->nullable()->comment('講演者メニュー表示可否')->after('speakerFlag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
}
