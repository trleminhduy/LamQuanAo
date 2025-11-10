<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_variant_id', 'quantity'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function productVariant() {
        return $this->belongsTo(ProductVariant::class);
    }
}
