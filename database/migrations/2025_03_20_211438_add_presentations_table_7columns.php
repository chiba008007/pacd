<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresentationsTable7columns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presentations', function (Blueprint $table) {
            //
            $table->text('enjya1')->nullable()->comment('演者名')->after('description');
            $table->text('enjya2')->nullable()->comment('演者名')->after('enjya1');
            $table->text('enjya3')->nullable()->comment('演者名')->after('enjya2');
            $table->text('enjya4')->nullable()->comment('演者名')->after('enjya3');
            $table->text('enjya5')->nullable()->comment('演者名')->after('enjya4');
            $table->text('enjya6')->nullable()->comment('演者名')->after('enjya5');
            $table->text('syozoku1')->nullable()->comment('所属名')->after('enjya6');
            $table->text('syozoku2')->nullable()->comment('所属名')->after('syozoku1');
            $table->text('syozoku3')->nullable()->comment('所属名')->after('syozoku2');
            $table->text('syozoku4')->nullable()->comment('所属名')->after('syozoku3');
            $table->text('syozoku5')->nullable()->comment('所属名')->after('syozoku4');
            $table->text('syozoku6')->nullable()->comment('所属名')->after('syozoku5');
            $table->text('enjya_other')->nullable()->comment('備考')->after('syozoku6');

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
