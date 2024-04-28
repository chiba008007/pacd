<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableWebexUrlToProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('webex_url');
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->string('webex_url')->nullable()->after('date')->comment('webexurl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('webex_url');
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->string('webex_url')->nullable(false)->after('date')->comment('webexurl');
        });
    }
}
