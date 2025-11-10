<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['full_name', 'email', 'phone_number', 'message', 'is_reply'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
