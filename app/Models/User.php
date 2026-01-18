<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; para verificaciones por correo mas adelante
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'rol',
        'estado',
        'email',
        'password',
    ];

    protected $appends = ['nombre_completo'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'estado' => 'boolean',
    ];

   public function getNombreCompletoAttribute(): string
    {
        $partes = trim(($this->nombre ?? '') . ' ' . ($this->apellido_paterno ?? '') . ' ' . ($this->apellido_materno ?? ''));
        return $partes !== '' ? $partes : (string) ($this->name ?? '');
    }


    public function cliente()
    {
        return $this->hasOne(\App\Models\Cliente::class);
    }

    public function profesional()
    {
        return $this->hasOne(\App\Models\Profesional::class);
    }
}
