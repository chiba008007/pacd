<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCapacityToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // 'events'テーブルに 'capacity' という名前の整数(integer)カラムを追加する
            $table->integer('capacity')->comment('定員');
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
            // ロールバック時に 'capacity' カラムを削除する
            $table->dropColumn('capacity');
        });
    }
}
