<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPresentationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presentations', function (Blueprint $table) {
            //
            $table->text('daimoku')->nullable()->after('description')->comment('題目');  //カラム追加
            $table->text('enjya')->nullable()->after('daimoku')->comment('発表縁者');  //カラム追加
            $table->text('syozoku')->nullable()->after('enjya')->comment('所属');  //カラム追加
            $table->text('gaiyo')->nullable()->after('syozoku')->comment('概要');  //カラム追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presentations', function (Blueprint $table) {
            //
        });
    }
}
