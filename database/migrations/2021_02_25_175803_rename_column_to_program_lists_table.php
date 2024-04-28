<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnToProgramListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_lists', function (Blueprint $table) {
            $table->dropColumn('speak_id');
            $table->integer('presentation_id')->nullable()->after('program_id');
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
            $table->dropColumn('presentation_id');
            $table->integer('speak_id')->length(11)->nullable()->comment("講演者テーブルのID")->after('end_minute');
        });
    }
}
