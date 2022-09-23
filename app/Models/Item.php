<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'offer_id',
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

    public function OfferItem()
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Gaza')->format('Y-m-d H:i');
    }


    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Gaza')->format('Y-m-d H:i');
    }
}
