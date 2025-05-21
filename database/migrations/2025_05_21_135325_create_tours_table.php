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
                $table->string('punto_salida', 50);
                $table->string('destino', 60);
                $table->date('fecha_inicio');
                $table->date('fecha_final');
                $table->decimal('precio', 6, 2);
                $table->string('duracion', 10);
                $table->string('cupo_maximo', 20);
                $table->string('itinerario', 100);
                $table->string('transporte', 20);
                $table->string('idioma', 20);
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
