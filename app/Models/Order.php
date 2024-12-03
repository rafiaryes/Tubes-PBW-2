<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Order extends Model
{
    protected $table = "orders";
    protected $guarded = [
        ""
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
