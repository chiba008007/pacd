<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipeStatusToAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendees', function (Blueprint $table) {
            //
            $table->string('invoice_date')->nullable()->after('is_enabled_invoice')->comment('請求書ダウンロード日付');  //カラム追加
            $table->string('recipe_date')->nullable()->after('is_enabled_invoice')->comment('領収書ダウンロード日付');  //カラム追加
            $table->integer('invoice_status')->nullable()->default(0)->after('is_enabled_invoice')->comment('請求書ダウンロードステータス');  //カラム追加
            $table->integer('recipe_status')->nullable()->default(0)->after('is_enabled_invoice')->comment('領収書ダウンロードステータス');  //カラム追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendees', function (Blueprint $table) {
            //
        });
    }
}
