<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->integer('doc_dl')
            ->default(0)
            ->after('is_paid')
            ->comment('0:資料ダウンロード不可,1:資料ダウンロード可')
            ->nullable(false);//カラム追加
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
            $table->dropColumn('doc_dl');
        });
    }
}
