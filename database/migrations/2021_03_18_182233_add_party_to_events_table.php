<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartyToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('events')) {
            // テーブルが存在していればリターン
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            //
            $table->string('party',512)->nullable()->after('event_address')->comment('懇親会場所');
            $table->string('party_address',512)->nullable()->after('party')->comment('懇親会住所');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
}
