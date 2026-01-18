<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesional extends Model
{
    protected $table = 'profesionales';

    protected $fillable = [
        'user_id',
        'especialidad',
        'anios_experiencia',
        'estado',
    ];

    protected $casts = [
        'anios_experiencia' => 'integer',
        'estado' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function bloqueosAgenda(): HasMany
    {
        return $this->hasMany(BloqueoAgenda::class);
    }
}
