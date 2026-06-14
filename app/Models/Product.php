<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'time',
        'category_id',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    public function category() {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function cart(){
        return $this->belongsToMany(Cart::class);
    }

    public function favorite(){
        return $this->belongsToMany(Favorite::class);
    }
}
