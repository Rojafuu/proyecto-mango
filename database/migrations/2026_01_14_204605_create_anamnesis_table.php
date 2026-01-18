<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anamnesis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->unique()
                ->constrained('clientes')
                ->cascadeOnDelete();

            $table->string('alergias', 255)->nullable();
            $table->string('enfermedades', 255)->nullable();
            $table->string('farmacos', 255)->nullable();
            $table->string('infecciones_relevantes', 255)->nullable();
            $table->string('procedimientos_previos', 255)->nullable();
            $table->string('observaciones', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anamnesis');
    }
};
