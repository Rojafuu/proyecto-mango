<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'user_id',
        'fecha_nac',
        'telefono',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nac' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function anamnesis(): HasOne
    {
        return $this->hasOne(Anamnesis::class);
    }
}
