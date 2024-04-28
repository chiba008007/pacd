<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_inputs', function (Blueprint $table) {
            $table->id();
            $table->integer('category_type')->length(1)->comment('1:共通, 2:例会, 3:討論会, 4:技術講習会');
            $table->string('name')->comment('項目名');
            $table->integer('type')->length(1)->default(1)->comment('1:textbox, 2:selectbox, 3:checkbox');
            $table->string('validation_rules')->nullable();
            $table->text('validation_message')->nullable();
            $table->boolean('is_display_published')->default(0)->comment('公開画面に表示するか');
            $table->boolean('is_display_user_list')->default(0)->comment('ユーザー一覧に表示するか');
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
        Schema::dropIfExists('form_inputs');
    }
}
