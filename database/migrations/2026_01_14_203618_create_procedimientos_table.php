<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimientos', function (Blueprint $table) {
            $table->id();

            $table->string('tipo', 20);                 // tatuaje / piercing
            $table->string('zona_cuerpo', 100);
            $table->string('estilo', 100)->nullable();

            $table->decimal('ancho_cm', 5, 2)->nullable();
            $table->decimal('alto_cm', 5, 2)->nullable();

            $table->string('descripcion', 255)->nullable();
            $table->unsignedInteger('duracion_estimada'); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimientos');
    }
};
