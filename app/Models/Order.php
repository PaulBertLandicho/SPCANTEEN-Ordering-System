<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'status_id',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'order_product',
            'order_id',
            'product_id'
        );
    }
    public function items()
    {
        return $this->hasMany(Cart::class);
    }
}
