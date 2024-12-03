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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements("id")->primary(true);
            $table->string('user_id')->nullable();
            $table->foreignId('kasir_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string("status");
            $table->string("name")->nullable();
            $table->string("email")->nullable();
            $table->string("nophone")->nullable();
            $table->string("payment_method")->nullable();
            $table->string("order_method")->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->decimal("total_price", 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_orders');
    }
};
