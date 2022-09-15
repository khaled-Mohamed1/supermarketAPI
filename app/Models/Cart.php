<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'offer_id',
        'product_quantity',
    ];

    public function UserOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'user_id', 'id');
    }

    public function ProductCart(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function OfferCart(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id', 'id');
    }

}
