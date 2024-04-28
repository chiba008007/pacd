<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfstoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdfstorages', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->nullable()->comment("ユーザID");
            $table->integer("event_id")->nullable()->comment("イベントID");
            $table->text("filenamecode")->nullable()->comment("ファイル名ユニーク文字");
            $table->text("filename")->nullable()->comment("ファイル名JP");
            $table->integer("type")->nullable()->comment("1:請求書 2:領収書");
            $table->date("create_date")->nullable()->comment("日付");
            $table->integer("status")->default(1)->comment("ステータス");
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
        Schema::dropIfExists('pdfstorages');
    }
}
