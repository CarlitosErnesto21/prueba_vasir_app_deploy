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
        Schema::create('detalles_reservas_aerolineas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('clase', ['PRIMERA_CLASE', 'CLASE_EJECUTIVA', 'TURISTA_PREMIUM', 'CLASE_TURISTA']);
            $table->string('numero_vuelo', 10);
            $table->string('origen', 50);
            $table->string('destino', 50);
            $table->datetime('fecha_salida');
            $table->datetime('fecha_llegada');
            $table->string('asiento', 10);
            $table->decimal('precio', 7, 2);
            // llaves foraneeas a reservas y aerolineas
            $table->unsignedBigInteger('reserva_id');
            $table->unsignedBigInteger('aerolinea_id');
            $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
            $table->foreign('aerolinea_id')->references('id')->on('aerolineas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_reservas_aerolineas');
    }
};
