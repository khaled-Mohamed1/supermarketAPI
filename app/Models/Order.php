<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
    ];

    public function UserOrder()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    protected $casts = [
        'created_at' => "datetime:Y-m-d H:m",
        'updated_at' => "datetime:Y-m-d H:m",
    ];

}
