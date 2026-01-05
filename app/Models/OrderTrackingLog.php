<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTrackingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'location',
        'updated_date',
        'note',
    ];

    /**
     * Cast updated_date thành kiểu datetime
     */
    protected $casts = [
        'updated_date' => 'datetime',
    ];

    /**
     * Mặc định sắp xếp theo thời gian mới nhất trước
     */
    protected static function booted()
    {
        static::addGlobalScope('order', function ($builder) {
            $builder->orderBy('updated_date', 'desc');
        });
    }

    /**
     * Relationship: OrderTrackingLog thuộc về 1 Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
