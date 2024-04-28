<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventNameToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('events')) {
            // テーブルが存在していればリターン
            return;
        }
        Schema::table('events', function (Blueprint $table) {
            //
            $table->string('sponser',512)->nullable()->after('code')->comment('イベント名');
            $table->text('event_info')->nullable()->after('sponser')->comment('イベント概要');
            $table->time('date_start_time')->nullable()->after('date_start')->comment('開始時間');
            $table->time('date_end_time')->nullable()->after('date_end')->comment('終了時間');
            $table->text('event_detail')->nullable()->after('other')->comment('イベント詳細');

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
