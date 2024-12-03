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
        // Schema::create('tabel_transactions', function (Blueprint $table) {
        //     $table->bigIncrements("id")->primary(true);
        //     $table->unsignedBigInteger("order_id");
        //     $table->timestamp("transaction_date");
        //     $table->bigInteger("amount");
        //     $table->foreign("order_id")->references("id")->on("orders")->onDelete("CASCADE");
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_transactions');
    }
};
