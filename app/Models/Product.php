<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock', 'category_id', 'supplier_id'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems() {
        return $this->hasMany(CartItem::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function firstImage(){
        return $this->hasOne(ProductImage::class)->orderBy('id', 'ASC');
    }
}
