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
            $table->string('nombre');
            $table->enum('categoria', ['NACIONAL', 'INTERNACIONAL']);
            $table->text('incluye')->nullable();
            $table->text('no_incluye')->nullable();
            $table->integer('cupo_min');
            $table->integer('cupo_max');
            $table->string('punto_salida');
            $table->datetime('fecha_salida');
            $table->datetime('fecha_regreso');
            $table->enum('estado', ['DISPONIBLE', 'AGOTADO', 'EN_CURSO', 'COMPLETADO', 'CANCELADO', 'SUSPENDIDO', 'REPROGRAMADO'])->default('DISPONIBLE');
            $table->decimal('precio', 10, 2);
            // llaves foraneas a categorias_tours y transportes
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