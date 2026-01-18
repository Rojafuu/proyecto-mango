<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anamnesis extends Model
{
    protected $table = 'anamnesis';

    protected $fillable = [
        'cliente_id',
        'alergias',
        'enfermedades',
        'farmacos',
        'infecciones_relevantes',
        'procedimientos_previos',
        'observaciones',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
