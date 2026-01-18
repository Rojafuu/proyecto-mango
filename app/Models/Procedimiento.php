<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procedimiento extends Model
{
    protected $table = 'procedimientos';

    protected $fillable = [
        'tipo',
        'zona_cuerpo',
        'estilo',
        'ancho_cm',
        'alto_cm',
        'descripcion',
        'duracion_estimada',
    ];

    protected $casts = [
        'ancho_cm' => 'float',
        'alto_cm' => 'float',
        'duracion_estimada' => 'integer',
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
