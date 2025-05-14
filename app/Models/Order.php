<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Traits\HasCustomOrderNumber;

class Order extends Model
{
    use HasCustomOrderNumber;
    protected $table = "orders";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    protected $guarded = [
        ""
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasOne(Payment::class);
    }

    // kasir
    public function kasir()
    {
        return $this->hasOne(User::class, 'id', 'kasir_id');
    }
}
