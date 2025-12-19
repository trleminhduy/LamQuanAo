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

            // mặc định sẽ là phương thức giao hàng bowi nhân viên
            $table->string('shipping_method')->default('internal')->after('shipping_address_id');
            
            // mã đơn hàng GHN 
            $table->string('ghn_order_code')->nullable()->after('shipping_method');
            
            // mhí ship thực tế từ GHN
            $table->decimal('ghn_shipping_fee', 10, 2)->nullable()->after('ghn_order_code');
            
            // thời gian dự kiến giao 
            $table->timestamp('ghn_expected_delivery')->nullable()->after('ghn_shipping_fee');
            
            // trạng thái 
            $table->string('ghn_status')->nullable()->after('ghn_expected_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_method',
                'ghn_order_code',
                'ghn_shipping_fee',
                'ghn_expected_delivery',
                'ghn_status'
            ]);
        });
    }
};
