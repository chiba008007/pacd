<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::dropIfExists('account_types');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login_id', 50)->unique()->comment('会員ID');
            $table->string('sei', 100);
            $table->string('mei', 100);
            $table->string('sei_kana', 100);
            $table->string('mei_kana', 100);
            $table->string('email');
            $table->string('password');
            $table->text('remarks')->nullable();
            $table->integer('type')->length(1)->default(1)->comment('会員区分(1:無料,2:個人,3:法人,4:協賛)');
            $table->rememberToken();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
