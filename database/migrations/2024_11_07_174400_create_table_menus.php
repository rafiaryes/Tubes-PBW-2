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
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements("id")->primary(true);
            $table->string("nama_menu");
            $table->string("deskripsi_menu");
            $table->bigInteger("price");
            $table->string("status_menu");
            $table->string("image_menu");
            $table->timestamp("craeted_at");
            $table->timestamp("updated_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_menus');
    }
};
