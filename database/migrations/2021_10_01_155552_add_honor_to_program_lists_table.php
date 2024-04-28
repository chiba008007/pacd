<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHonorToProgramListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_lists', function (Blueprint $table) {
            //
            $table->integer('honor')->nullable()->default(0)->after('note')->comment('敬称');  //カラム追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_lists', function (Blueprint $table) {
            //
        });
    }
}
