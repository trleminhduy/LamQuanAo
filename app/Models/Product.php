<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "name",
        "slug",
        "category_id",
        "price",
        "stock",
        "status",
        "unit",
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function images(){
    return $this ->hasMany(ProductImage::class);
    }

    public function cartItems(){
        return $this ->hasMany(CartItem::class);
    }

    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}
}
