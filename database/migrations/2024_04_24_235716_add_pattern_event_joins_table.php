<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatternEventJoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_joins', function (Blueprint $table) {
            //
            $table->integer('pattern')->default(1)->comment('講演・講習:1 懇親会:2')->after('join_fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_joins', function (Blueprint $table) {
            //
        });
    }
}
