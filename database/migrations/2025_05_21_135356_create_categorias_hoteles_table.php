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
        Schema::create('categorias_hoteles', function (Blueprint $table) {
            $table->id();
            $table->enum('estrella', ['1 estrella', '2 estrellas', '3 estrellas', '4 estrellas', '5 estrellas']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_hoteles');
    }
};
