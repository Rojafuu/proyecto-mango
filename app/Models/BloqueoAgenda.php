<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';

    protected $fillable = [
        'profesional_id',
        'inicio',
        'fin',
        'motivo',
        'estado',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
        'estado' => 'boolean',
    ];

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(Profesional::class);
    }
}
