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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('descripcion', 100);
            $table->string('punto_salida', 60);
            $table->dateTime('fecha');
            $table->decimal('precio', 5, 2);
            $table->time('hora_regreso');
            // llavee foraneea a categorias_tours y transportes
            $table->unsignedBigInteger('categoria_tour_id');
            $table->unsignedBigInteger('transporte_id');
            $table->foreign('categoria_tour_id')->references('id')->on('categorias_tours')->onDelete('cascade');
            $table->foreign('transporte_id')->references('id')->on('transportes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
