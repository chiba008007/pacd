<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCategoryTypeToFormTypeOnFormInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_inputs', function (Blueprint $table) {
            try {
                $table->dropColumn('category_type');
                $table->integer('form_type')->length(1)->after('id');
            } catch (\Exception $e) {
            }
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
            $table->dropColumn('form_type');
        });
    }
}
