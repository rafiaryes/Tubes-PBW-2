<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        "order_status","total_price","order_date","updated_at","delivery_type"
    ];
}
