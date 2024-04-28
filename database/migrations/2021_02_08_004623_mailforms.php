<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Mailforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailforms', function (Blueprint $table) {
            $table->id();
            $table->integer('form_type')->length(11)->nullable(false);
            $table->text('title')->nullable(false)->comment("タイトル");
            $table->text('note')->nullable(false)->comment("内容");
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
        Schema::dropIfExists('mailforms');
    }
}
