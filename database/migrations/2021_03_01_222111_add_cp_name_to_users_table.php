<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCpNameToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cp_name')->nullable()->after('type_number')->comment('法人名');  //カラム追加
            $table->string('address')->nullable()->after('cp_name')->comment('住所');  //カラム追加
            $table->string('busyo')->nullable()->after('address')->comment('部署');  //カラム追加
            $table->string('tel')->nullable()->after('busyo')->comment('電話');  //カラム追加
            $table->string('groupname')->nullable()->after('tel')->comment('部署');  //カラム追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
