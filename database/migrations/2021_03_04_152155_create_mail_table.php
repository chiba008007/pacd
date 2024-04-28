<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->string('body')->nullable();
            $table->integer('event_id');
            $table->integer('sender_type')->default(1)->comment("1:参加者 2:講演者");
            $table->integer('sender_status')->default(0)->comment("0:配信まち 1:送信済み");
            $table->dateTime('senddate')->nullable()->comment("配信時間");
            $table->integer('status')->default(1)->comment("0無効 1有効");
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
        Schema::dropIfExists('mails');
    }
}
