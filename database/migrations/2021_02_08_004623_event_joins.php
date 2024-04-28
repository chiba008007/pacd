<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EventJoins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_joins', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->length(11)->nullable(false)->comment("eventsテーブルのid");
            $table->integer('number')->length(11)->nullable(false)->comment("並び順");
            $table->integer('join_status')->length(1)->default(1)->comment("有効/無効");
            $table->string('join_name',512)->nullable()->comment("項目名");
            $table->integer('join_price')->length(11)->default(0)->comment("参加金額");
            $table->integer('join_fee')->length(11)->default(0)->comment("懇談会金額");

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
        Schema::dropIfExists('event_joins');
    }
}
