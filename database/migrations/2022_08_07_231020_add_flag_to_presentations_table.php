<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagToPresentationsTable extends Migration
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
            $table->boolean('proceeding_flag')->default(0)->comment('出力可否')->after('proceeding');
            $table->boolean('flash_flag')->default(0)->comment('出力可否')->after('flash');
            $table->boolean('poster_flag')->default(0)->comment('出力可否')->after('poster');
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
