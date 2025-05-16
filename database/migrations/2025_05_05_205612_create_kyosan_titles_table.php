<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKyosanTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyosanTitles', function (Blueprint $table) {
            $table->id();
            $table->text("tenjikaiTitle")->nullable()->comment("展示参加者タイトル");
            $table->text("tenjikaiNote")->nullable()->comment("展示参加者内容");
            $table->text("konsinkaiTitle")->nullable()->comment("懇親会参加者タイトル");
            $table->text("konsinkaiNote")->nullable()->comment("会参加者タイトル");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kyosanTitles');
    }
}
