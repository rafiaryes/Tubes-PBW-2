<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = "merchants";
    protected $fillable = [
        "nama_menu", "deskripsi_menu", "price", "status_menu", "image_menu", "created_at", "updated_at" 
    ];
}
