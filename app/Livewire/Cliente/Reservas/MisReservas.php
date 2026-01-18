<?php

namespace App\Livewire\Cliente\Reservas;

use App\Models\Cliente;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MisReservas extends Component
{

    public ?int $verPropuestaId = null;

    public function cancelarConfirmado(int $id): void
    {
        $cliente = Cliente::where('user_id', Auth::id())->first();

        if (!$cliente) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'Perfil faltante',
                text: 'Tu usuario no tiene perfil de cliente asociado.',
                confirmButtonText: 'OK',
            );
            return;
        }

        $reserva = Reserva::where('id', $id)
            ->where('cliente_id', $cliente->id)
            ->first();

        if (!$reserva) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'No encontrada',
                text: 'No se encontró la reserva o no te pertenece.',
                confirmButtonText: 'OK',
            );
            return;
        }

        if (!in_array($reserva->estado, ['pendiente', 'confirmada'])) {
            $this->dispatch('swal',
                icon: 'info',
                title: 'No se puede cancelar',
                text: 'Esta reserva ya no está en un estado cancelable.',
                confirmButtonText: 'OK',
            );
            return;
        }

        $reserva->update(['estado' => 'cancelada']);

        $this->dispatch('swal',
            icon: 'success',
            title: 'Reserva cancelada',
            text: 'Tu reserva fue cancelada.',
            confirmButtonText: 'OK',
        );
    }



public function verPropuesta(int $id): void
{
    $this->verPropuestaId = ($this->verPropuestaId === $id) ? null : $id;
}

public function aceptarPropuesta(int $id): void
{
    $cliente = Cliente::where('user_id', Auth::id())->first();
    if (!$cliente) return;

    $reserva = Reserva::where('id', $id)
        ->where('cliente_id', $cliente->id)
        ->first();

    if (!$reserva || $reserva->estado !== 'propuesta') return;

    $reserva->update(['estado' => 'aceptada']);

    $this->dispatch('swal',
        icon: 'success',
        title: 'Propuesta aceptada',
        text: 'Ahora falta la confirmación final del profesional.',
        confirmButtonText: 'OK',
    );
     $this->verPropuestaId = null;
}

    public function render()
    {
        $cliente = Cliente::where('user_id', Auth::id())->first();

        $reservas = collect();

        if ($cliente) {
            $reservas = Reserva::with(['profesional.user', 'procedimiento'])
                ->where('cliente_id', $cliente->id)
                ->orderByDesc('inicio')
                ->get();
        }

        return view('livewire.cliente.reservas.mis-reservas', [
            'reservas' => $reservas,
            'clienteExiste' => (bool) $cliente,
        ])->layout('layouts.app');
    }
}
