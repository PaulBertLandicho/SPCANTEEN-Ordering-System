<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id', // ADD THIS
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function getAvailabilityAttribute()
    {
        return $this->product->availability;
    }
}
