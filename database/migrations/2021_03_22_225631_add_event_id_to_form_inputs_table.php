<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventIdToFormInputsTable extends Migration
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
            $table->integer('event_id')->nullable()->default(0)->after('id')->comment('イベントID');  //カラム追加

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

        });
    }
}
