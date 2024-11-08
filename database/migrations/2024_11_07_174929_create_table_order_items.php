<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements("id")->primary(true);
            $table->unsignedBigInteger("order_id");
            $table->unsignedBigInteger("menu_id");
            $table->integer("quantity");
            $table->integer("price");
            $table->foreign("order_id")->references("id")->on("orders")->onDelete("CASCADE");
            $table->foreign("menu_id")->references("id")->on("menus")->onDelete("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_order_items');
    }
};
