<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'product_name',
        'product_description',
        'product_image',
        'product_quantity',
        'product_price',
        'order_qty',
    ];


    public function CategoryProduct()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
