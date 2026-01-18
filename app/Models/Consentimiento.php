<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consentimiento extends Model
{
    protected $table = 'consentimientos';

    protected $fillable = [
        'reserva_id',
        'aceptado',
        'fecha_aceptacion',
        'version_documento',
        'ip_registro',
        'pdf_url',
        'hash_documento',
    ];

    protected $casts = [
        'aceptado' => 'boolean',
        'fecha_aceptacion' => 'datetime',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }
}
