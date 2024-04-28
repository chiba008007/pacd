<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnToPresentationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('presentations', 'event_attendee_id')) {
            Schema::table('presentations', function (Blueprint $table) {
                $table->dropColumn('event_attendee_id');
            });
        }
        if (!Schema::hasColumn('presentations', 'presenter_id')) {
            Schema::table('presentations', function (Blueprint $table) {
                $table->integer('event_join_id')->after('user_id')->comment('参加費ID')->nullable();
            });
        }
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
