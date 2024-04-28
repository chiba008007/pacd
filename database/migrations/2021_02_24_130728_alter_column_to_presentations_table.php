<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnToPresentationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presentations', function (Blueprint $table) {
            if (Schema::hasColumn('presentations', 'number')) {
                Schema::table('presentations', function (Blueprint $table) {
                    $table->dropUnique('presentations_number_unique');
                    $table->dropColumn('number');
                });
            }
            Schema::table('presentations', function (Blueprint $table) {
                $table->string('number')->unique()->after('id')->comment('発表番号');
            });
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
