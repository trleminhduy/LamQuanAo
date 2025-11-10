<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use  HasFactory, Notifiable;

    protected $fillable = [
        "name",
        "email",
        "password",
        "status",
        "phone_number",
        "avatar",
        "address",
        "role_id",
        "activation_token",
        "google_id",
    ];

    protected $hidden = [
        "password",
        "remember_token",
    ];

    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    /* =============================
       Quan hệ với các bảng khác
    ============================= */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /* =============================
       Kiểm tra trạng thái tài khoản
    ============================= */

    public function isPending()
    {
        return $this->status === "pending";
    }

    public function isActive()
    {
        return $this->status === "active";
    }

    public function isBanned()
    {
        return $this->status === "banned";
    }

    public function isDeleted()
    {
        return $this->status === "deleted";
    }
}
