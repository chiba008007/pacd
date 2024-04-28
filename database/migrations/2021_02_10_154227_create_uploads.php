<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->length(11)->nullable(false)->comment("eventsテーブルのid");
            $table->string('filename',128)->nullable()->unique()->comment("ファイル名");
            $table->integer('type')->length(1)->nullable(false)->default(0)->comment("1:開催案内 2:報告  3:要旨 4:その他");
            $table->string('dispname',128)->nullable()->comment("表示名");
            $table->string('ext',11)->nullable(false)->comment("拡張子");
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
        Schema::dropIfExists('uploads');
    }
}
