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
            // khách vãng  lai
            $table->foreignId('user_id')->nullable()->change();

            //thêm field bổ sung vãng lai

            $table->string('guest_name')->nullable()->after('status');
            $table->string('guest_phone')->nullable()->after('guest_name');
            $table->string('guest_email')->nullable()->after('guest_phone');
        });


        Schema::table('shipping_addresses', function (Blueprint $table) {
            //thêm field bổ sung vãng lai

            $table->foreignId('user_id')->nullable()->change();

            //session id lưu địa chỉ vãng lai dựa vô session

            $table->string('session_id')->nullable()->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_email']);
        });

        Schema::table('shipping_addresses', function (Blueprint $table) {
            
            $table->dropColumn('session_id');
        });
    }
};
