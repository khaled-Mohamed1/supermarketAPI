<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_quantity',
        'price',
    ];

    public function OrderItem()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function ProductItem()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
