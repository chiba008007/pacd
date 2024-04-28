<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYearpaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yearpayments', function (Blueprint $table) {
            $table->id();
            $table->integer("year")->nullable()->comment("年度");
            $table->text("bank_name")->nullable()->comment("振込先");
            $table->text("bank_code")->nullable()->comment("口座名");
            $table->text("invoice_address")->nullable()->comment("請求書情報");
            $table->text("invoice_memo")->nullable()->comment("請求書メモ");
            $table->text("recipe_memo")->nullable()->comment("領収書メモ");

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
        Schema::dropIfExists('yearpayments');
    }
}
