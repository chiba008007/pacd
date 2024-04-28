<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCsvflag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_inputs', function (Blueprint $table) {
            //
            $table->boolean('csvflag')->default(0)->comment('csv出力可否')->after('is_display_user_list');
            $table->integer('csvtag')->default(0)->comment('csv出力位置指定')->after('csvflag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_inputs', function (Blueprint $table) {
            //
            $table->dropColumn('csvflag');
            $table->dropColumn('csvtag');
        });
    }
}
