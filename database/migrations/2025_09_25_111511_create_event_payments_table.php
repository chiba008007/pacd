<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateEventPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('event_payments', function (Blueprint $table) {
            // 主キー
            $table->id();

            // 外部キー
            $table->foreignId('attendee_id')->comment('参加申込ID')->constrained()->onDelete('cascade');

            // カラム定義
            $table->string('stripe_payment_intent_id')->unique()->comment('Stripe決済ID');
            $table->unsignedInteger('payment_amount')->comment('金額');
            $table->timestamp('payment_date')->nullable()->comment('支払日');
            $table->string('payment_status')->default('pending')->comment('決済ステータス');
            $table->timestamp('acknowledged_at')->nullable()->comment('管理者確認日時');
            
            // Laravel標準タイムスタンプ
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_payments');
    }
}