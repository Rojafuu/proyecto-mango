<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reserva_id')
                ->unique()
                ->constrained('reservas')
                ->cascadeOnDelete();

            $table->boolean('aceptado')->default(false);
            $table->timestamp('fecha_aceptacion')->nullable();

            $table->string('version_documento', 20);   
            $table->string('ip_registro', 45)->nullable();
            $table->string('pdf_url', 255)->nullable();
            $table->string('hash_documento', 64)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos');
    }
};
