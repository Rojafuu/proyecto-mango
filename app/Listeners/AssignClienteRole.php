<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignClienteRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
public function handle(Registered $event): void
{
    $user = $event->user;

    // Regla: registro público -> SOLO cliente
    $user->syncRoles(['cliente']);

    // Espejo en tabla users
    if ($user->rol !== 'cliente') {
        $user->rol = 'cliente';
    }
    if ($user->estado === null) {
        $user->estado = true;
    }
    $user->save();

    // Perfil cliente 1:1
    $user->cliente()->firstOrCreate([]);
}


}
