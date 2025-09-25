<?php
// database/migrations/xxxx_add_status_to_attendees_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            // 💡 改善点: 申込ステータスの種類を限定し、データの不整合を防ぎます
            $allowed_statuses = ['payment_pending', 'confirmed', 'cancelled'];
            $table->enum('status', $allowed_statuses)
                  ->default('payment_pending')
                  ->after('event_id')
                  ->comment('申込ステータス');
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};