<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendeesTable4columns extends Migration
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
            $table->text('tenjiSanka1Status')->nullable()->comment('展示参加者1')->after('discountSelectText');
            $table->text('tenjiSanka1Name')->nullable()->comment('展示参加者1名前')->after('tenjiSanka1Status');
            $table->text('tenjiSanka2Status')->nullable()->comment('展示参加者2')->after('tenjiSanka1Name');
            $table->text('tenjiSanka2Name')->nullable()->comment('展示参加者2名前')->after('tenjiSanka2Status');

            $table->text('konsinkaiSanka1Status')->nullable()->comment('懇親会参加者1')->after('tenjiSanka2Name');
            $table->text('konsinkaiSanka1Name')->nullable()->comment('懇親会参加者1名前')->after('konsinkaiSanka1Status');
            $table->text('konsinkaiSanka2Status')->nullable()->comment('懇親会参加者2')->after('konsinkaiSanka1Name');
            $table->text('konsinkaiSanka2Name')->nullable()->comment('懇親会参加者2名前')->after('konsinkaiSanka2Status');

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
