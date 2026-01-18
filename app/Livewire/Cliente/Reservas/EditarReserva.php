<?php

namespace App\Livewire\Cliente\Reservas;

use App\Models\BloqueoAgenda;
use App\Models\Cliente;
use App\Models\Profesional;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EditarReserva extends Component
{
    public Reserva $reserva;

    public string $tipo = '';
    public string $profesional_id = '';
    public string $inicio = '';   
    public int $bloques = 1;
    public string $observaciones = '';

    public ?string $finPreview = null;

    public function mount(Reserva $reserva): void
    {
        $cliente = Cliente::where('user_id', Auth::id())->first();

        if (!$cliente || $reserva->cliente_id !== $cliente->id) {
            abort(403);
        }

        $this->reserva = $reserva->load(['procedimiento', 'profesional']);

        $this->tipo = $this->reserva->procedimiento?->tipo ?? '';
        $this->profesional_id = (string) $this->reserva->profesional_id;

        $this->inicio = Carbon::parse($this->reserva->inicio)->format('Y-m-d\TH:i');

        $mins = (int) ($this->reserva->procedimiento?->duracion_estimada ?? 90);
        $this->bloques = max(1, min(3, (int) ceil($mins / 90)));

        $this->observaciones = (string) ($this->reserva->observaciones ?? '');

        $this->calcularFinPreview();
    }

    public function updated($property): void
    {
        if (in_array($property, ['inicio', 'bloques'])) {
            $this->calcularFinPreview();
        }
    }

    private function duracionEnMinutos(): int
    {
        $b = (int) $this->bloques;
        if (!in_array($b, [1, 2, 3])) $b = 1;
        return $b * 90;
    }

    private function calcularFinPreview(): void
    {
        $this->finPreview = null;
        if (!$this->inicio) return;

        $inicio = Carbon::parse($this->inicio);
        $fin = $inicio->copy()->addMinutes($this->duracionEnMinutos());

        $this->finPreview = $fin->format('Y-m-d H:i');
    }

    private function swalError(string $title, string $text): void
    {
        $this->dispatch('swal',
            icon: 'error',
            title: $title,
            text: $text,
            confirmButtonText: 'OK',
        );
    }

    public function guardar(): void
    {
        $this->validate([
            'tipo' => ['required', 'in:tatuaje,bodypiercing'],
            'profesional_id' => ['required', 'exists:profesionales,id'],
            'inicio' => ['required', 'date'],
            'bloques' => ['required', 'integer', 'in:1,2,3'],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ]);

        if (!in_array($this->reserva->estado, ['pendiente', 'confirmada'])) {
            $this->swalError('No editable', 'Esta reserva no se puede editar en su estado actual.');
            return;
        }

        $inicio = Carbon::parse($this->inicio);
        $fin = $inicio->copy()->addMinutes($this->duracionEnMinutos());

        if ($inicio->lessThan(Carbon::now())) {
            $this->swalError('Fecha inválida', 'No puedes agendar en una fecha/hora pasada.');
            return;
        }

        $prof = Profesional::with('user')
            ->where('id', $this->profesional_id)
            ->where('estado', true)
            ->first();

        if (!$prof) {
            $this->swalError('Profesional no disponible', 'El profesional seleccionado no está activo.');
            return;
        }

        if (strtolower(trim($prof->especialidad)) !== strtolower(trim($this->tipo))) {
            $this->swalError('Selección inválida', 'El profesional no corresponde al tipo seleccionado.');
            return;
        }

        $hayBloqueo = BloqueoAgenda::where('profesional_id', $this->profesional_id)
            ->where('estado', true)
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('inicio', '<', $fin)
                    ->where('fin', '>', $inicio);
            })
            ->exists();

        if ($hayBloqueo) {
            $this->swalError('Horario bloqueado', 'El profesional tiene un bloqueo en ese horario.');
            return;
        }

        $hayReserva = Reserva::where('profesional_id', $this->profesional_id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('id', '!=', $this->reserva->id)
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('inicio', '<', $fin)
                    ->where('fin', '>', $inicio);
            })
            ->exists();

        if ($hayReserva) {
            $this->swalError('Horario no disponible', 'Ese bloque ya está tomado. Elige otra fecha/hora.');
            return;
        }

        $this->reserva->update([
            'profesional_id' => $this->profesional_id,
            'inicio' => $inicio,
            'fin' => $fin,
            'observaciones' => $this->observaciones ?: null,
        ]);

        if ($this->reserva->procedimiento) {
            $this->reserva->procedimiento->update([
                'duracion_estimada' => $this->duracionEnMinutos(),
            ]);
        }

        $this->dispatch('swal',
            icon: 'success',
            title: 'Reserva actualizada',
            text: 'Se guardaron los cambios correctamente.',
            confirmButtonText: 'Volver a mis reservas',
            redirect: route('cliente.reservas.index'),
        );
    }

    public function render()
    {
        $profesionales = collect();

        if ($this->tipo) {
            $profesionales = Profesional::where('estado', true)
                ->whereRaw('LOWER(TRIM(especialidad)) = ?', [strtolower(trim($this->tipo))])
                ->with('user')
                ->orderBy('id')
                ->get();
        }

        return view('livewire.cliente.reservas.editar-reserva', [
            'profesionales' => $profesionales,
        ]);
    }
}
