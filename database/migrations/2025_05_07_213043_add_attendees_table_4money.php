<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendeesTable4money extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendees', function (Blueprint $table) {
            //
             $table->integer('tenjiSanka1Money')->nullable()->comment('展示参加者1金額')->after('tenjiSanka1Name');
             $table->integer('tenjiSanka2Money')->nullable()->comment('展示参加者2金額')->after('tenjiSanka2Name');
             $table->integer('konsinkaiSanka1Money')->nullable()->comment('懇親会参加者1金額')->after('konsinkaiSanka1Name');
             $table->integer('konsinkaiSanka2Money')->nullable()->comment('懇親会参加者2金額')->after('konsinkaiSanka2Name');
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
        });
    }
}
