<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer("enabled")->length(1)->default(1)->comment("0:申込受付済み 1:申込受付中");
            $table->integer("category_type")->length(1)->default(1)->comment("1:共通, 2:例会, 3:高分子分析討論会, 4:講習会");
            $table->string('code',255)->nullable(false)->unique()->comment("イベントコード");
            $table->string('name',255)->nullable(false)->comment("主催");
            $table->string('coworker',512)->nullable()->comment("協賛");
            $table->date('date_start')->comment("開催日");
            $table->date('date_end')->comment("閉会日");
            $table->string('place',1280)->nullable()->comment("場所");
            $table->string('event_address',1280)->nullable()->comment("住所");
            $table->integer('map_status')->length(1)->default(0)->comment("地図の表示");
            $table->string('webex_url',512)->nullable()->comment("webex用URL");
            $table->text('other')->nullable()->comment("備考");
            $table->integer('join_enable')->length(1)->default(1)->comment("懇談会参加");
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
        Schema::dropIfExists('events');
    }
}
