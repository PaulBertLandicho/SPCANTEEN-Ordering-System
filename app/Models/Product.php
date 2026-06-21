<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use App\Models\Order;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'size',
        'measurement',
        'unit',
        'image',
        'time',
        'category_id',
    ];

    public function getDisplaySizeAttribute()
    {
        $parts = [];

        // size (Small, Medium, Large)
        if (!empty($this->size)) {
            $parts[] = $this->size;
        }

        // measurement + unit (12oz, 500ml)
        $measurement = '';

        if (!empty($this->measurement) && !empty($this->unit)) {
            $measurement = $this->measurement . $this->unit;
        }

        if (!empty($measurement)) {
            $parts[] = $measurement;
        }

        return count($parts) ? implode(' - ', $parts) : null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cart()
    {
        return $this->belongsToMany(Cart::class);
    }

    public function favorite()
    {
        return $this->belongsToMany(Favorite::class);
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
    public function orderItems()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }
}
