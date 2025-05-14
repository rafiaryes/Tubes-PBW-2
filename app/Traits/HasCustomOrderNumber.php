<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasCustomOrderNumber
{
    public static function bootHasCustomOrderNumber()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $time = now()->format('His');

        // Ambil order terakhir hari ini
        $today = now()->toDateString();
        $lastOrder = static::whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->first();

        if ($lastOrder) {
            $parts = explode('/', $lastOrder->getKey());
            $lastNumber = isset($parts[3]) ? intval($parts[3]) : 0;
            $nextNumber = $lastNumber + 1;
            // 2 digit jika < 100, lebih dari itu tanpa leading zero
            $orderNumber = $nextNumber < 100 ? str_pad($nextNumber, 2, '0', STR_PAD_LEFT) : (string)$nextNumber;
        } else {
            $orderNumber = '01';
        }

        return "{$prefix}-{$date}-{$time}-{$orderNumber}";
    }
}
