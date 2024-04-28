<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipeDateTypeToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_date')->nullable()->after('status')->comment('請求書ダウンロード日付');  //カラム追加
            $table->string('recipe_date')->nullable()->after('status')->comment('領収書ダウンロード日付');  //カラム追加
            $table->integer('invoice_status')->nullable()->default(0)->after('status')->comment('請求書ダウンロードステータス');  //カラム追加
            $table->integer('recipe_status')->nullable()->default(0)->after('status')->comment('領収書ダウンロードステータス');  //カラム追加

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
