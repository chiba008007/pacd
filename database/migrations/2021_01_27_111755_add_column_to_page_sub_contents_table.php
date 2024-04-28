<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPageSubContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_sub_contents', function (Blueprint $table) {
            $table->integer('column_count')->default(2)->after('content2')->comment('列数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_sub_contents', function (Blueprint $table) {
            $table->dropColumn('column_count');
        });
    }
}
