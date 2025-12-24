<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'delivery_user_id',
        'delivery_note',
        'delivery_started_at',
        'delivery_completed_at',
        'shipping_address_id',
        'total_price',
        'status',
        'shipping_method',
        'ghn_order_code',
        'ghn_shipping_fee',
        'ghn_expected_delivery',
        'ghn_status',
        'guest_name',
        'guest_phone',
        'guest_email',
    ];

    protected $casts = [
        'delivery_started_at' => 'datetime',
        'delivery_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    public function deliveryUser()
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }
}
