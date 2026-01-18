<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'cliente_id',
        'profesional_id',
        'procedimiento_id',
        'inicio',
        'fin',
        'estado',
        'observaciones',

        // Propuesta del profesional
        'nota_profesional',
        'precio_estimado',
        'duracion_estimada_bloques',
        'sugerencia_fecha_hora',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'sugerencia_fecha_hora' => 'datetime', 
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(Profesional::class);
    }

    public function procedimiento(): BelongsTo
    {
        return $this->belongsTo(Procedimiento::class);
    }

    public function consentimiento(): HasOne
    {
        return $this->hasOne(Consentimiento::class);
    }
}
