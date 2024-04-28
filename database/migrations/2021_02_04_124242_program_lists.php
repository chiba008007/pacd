<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class ProgramLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->length(11)->nullable(false)->comment("eventsテーブルのid");
            $table->integer('program_id')->length(11)->nullable(false)->comment("programsテーブルのid");
            $table->integer('number')->length(11)->nullable(false)->comment("並び順");
            $table->integer('enable')->length(1)->default(1)->comment("有効/無効");
            $table->integer('ampm')->length(1)->default(1)->comment("1:午前 2:午後");
            $table->string('start_hour',11)->nullable()->comment("開始時");
            $table->string('start_minute',11)->nullable()->comment("開始分");
            $table->string('end_hour',11)->nullable()->comment("終了時");
            $table->string('end_minute',11)->nullable()->comment("終了分");
            $table->integer('speak_id')->length(11)->nullable()->comment("講演者テーブルのID");
            $table->text('note')->nullable()->comment("表示内容");
            $table->integer('status')->length(1)->default(1)->comment("ステータス");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('program_lists');
    }
}
