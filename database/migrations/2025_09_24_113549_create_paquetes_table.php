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
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->date('fecha_salida');
            $table->date('fecha_regreso');
            $table->text('incluye');
            $table->text('condiciones');
            $table->text('recordatorio');
            $table->decimal('precio', 10, 2);
            $table->unsignedBigInteger('pais_id');
            $table->unsignedBigInteger('provincia_id');
            $table->foreign('pais_id')->references('id')->on('paises')->onDelete('restrict');
            $table->foreign('provincia_id')->references('id')->on('provincias')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paquetes');
    }
};
