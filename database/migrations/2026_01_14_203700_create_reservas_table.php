<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('profesional_id')->constrained('profesionales')->cascadeOnDelete();
            $table->foreignId('procedimiento_id')->constrained('procedimientos')->cascadeOnDelete();

            $table->dateTime('inicio');
            $table->dateTime('fin');

            $table->string('estado', 20)->default('pendiente'); //pendiente/propuesta/confirmada/cancelada/completada
            $table->string('observaciones', 255)->nullable();

            // Propuesta del profesional (para revisar solicitud y sugerir)
            $table->text('nota_profesional')->nullable();
            $table->unsignedInteger('precio_estimado')->nullable(); 
            $table->unsignedTinyInteger('duracion_estimada_bloques')->nullable(); // cantidad de bloques
            $table->dateTime('sugerencia_fecha_hora')->nullable(); // sugerencia de agenda


            $table->timestamps();

            $table->index(['profesional_id', 'inicio']);
            $table->index(['cliente_id', 'inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
