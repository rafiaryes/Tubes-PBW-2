<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;
use App\Models\Order;

class OrderItem extends Model
{
    protected $table = "order_items";
    public $timestamps = false;
    protected $guarded = [
       ""
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
