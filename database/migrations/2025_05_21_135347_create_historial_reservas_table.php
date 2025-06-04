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
        Schema::create('historial_reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo_reserva', 20);
            $table->string('nombre_servicio', 100);
            $table->string('nombre_cliente', 150);
            $table->dateTime('fecha_reserva');
            $table->dateTime('fecha_confirmacion')->nullable();
            $table->dateTime('fecha_completado')->nullable();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('precio_total', 8, 2);
            $table->dateTime('fecha_registro');
            // Llave foránea a la tabla reservas
            $table->unsignedBigInteger('reserva_id')->nullable();
            $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_reservas');
    }
};
