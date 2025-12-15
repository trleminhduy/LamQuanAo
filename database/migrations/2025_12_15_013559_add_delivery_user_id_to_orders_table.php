<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
             // Phân công nhân viên giao hàng
            $table->foreignId('delivery_user_id')->nullable()->after('shipping_address_id')->constrained('users')->nullOnDelete();
            
            // note giao hàng
            $table->text('delivery_note')->nullable()->after('delivery_user_id');
            
            // Thời gian giao
            $table->timestamp('delivery_started_at')->nullable()->after('delivery_note');
            
            // Thời gian hoàn thành giao
            $table->timestamp('delivery_completed_at')->nullable()->after('delivery_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_user_id']);
            $table->dropColumn(['delivery_user_id', 'delivery_note', 'delivery_started_at', 'delivery_completed_at']);
        });
    }
};
