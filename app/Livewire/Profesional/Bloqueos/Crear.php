<?php

namespace App\Livewire\Profesional\Bloqueos;

use App\Models\BloqueoAgenda;
use App\Models\Profesional;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Crear extends Component
{
    public string $inicio = '';
    public string $fin = '';
    public string $motivo = '';

    public function guardar()
    {
        // Validación 
        $this->validate([
            'inicio' => ['required', 'date'],
            'fin'    => ['required', 'date', 'after:inicio'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ], [
            'inicio.required' => 'Debes indicar una fecha/hora de inicio.',
            'fin.required'    => 'Debes indicar una fecha/hora de fin.',
            'fin.after'       => 'La fecha/hora fin debe ser posterior al inicio.',
        ]);

        // Parseo consistente con datetime-local
        $inicioCarbon = Carbon::createFromFormat('Y-m-d\TH:i', $this->inicio, config('app.timezone'));
        $finCarbon    = Carbon::createFromFormat('Y-m-d\TH:i', $this->fin, config('app.timezone'));

        // No permitir pasado
        if ($inicioCarbon->lt(now(config('app.timezone')))) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'Fecha inválida',
                text: 'No puedes bloquear en una fecha/hora pasada.',
                confirmButtonText: 'OK',
            );
            return;
        }

        // Obtener profesional por user_id
        $profesional = Profesional::where('user_id', Auth::id())->first();

        if (!$profesional) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'No autorizado',
                text: 'Tu usuario no tiene perfil de profesional.',
                confirmButtonText: 'OK',
            );
            return;
        }

        // Anti-solape con BLOQUEOS activos
        $haySolape = BloqueoAgenda::where('profesional_id', $profesional->id)
            ->where('estado', true)
            ->where(function ($q) use ($inicioCarbon, $finCarbon) {
                $q->where('inicio', '<', $finCarbon)
                  ->where('fin', '>', $inicioCarbon);
            })
            ->exists();

        if ($haySolape) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'Horario ocupado',
                text: 'Ya tienes un bloqueo que se cruza con ese rango. Ajusta el inicio/fin.',
                confirmButtonText: 'OK',
            );
            return;
        }

        //  Anti-solape con RESERVAS (pendiente/confirmada)
        $hayReserva = Reserva::where('profesional_id', $profesional->id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where(function ($q) use ($inicioCarbon, $finCarbon) {
                $q->where('inicio', '<', $finCarbon)
                  ->where('fin', '>', $inicioCarbon);
            })
            ->exists();

        if ($hayReserva) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'No se puede bloquear',
                text: 'Ya existe una reserva en ese horario. Elige otro rango.',
                confirmButtonText: 'OK',
            );
            return;
        }

        //Guardar bloqueo
        BloqueoAgenda::create([
            'profesional_id' => $profesional->id,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'motivo' => $this->motivo ?: null,
            'estado' => true,
        ]);


        $this->reset(['inicio', 'fin', 'motivo']);

        $this->dispatch('swal',
            icon: 'success',
            title: 'Bloqueo de agenda creado',
            text: 'Se registró correctamente.',
            confirmButtonText: 'Ver mis bloqueos',
            redirect: route('profesional.bloqueos.index'),
        );
    }

    public function render()
    {
        return view('livewire.profesional.bloqueos.crear');
    }
}
