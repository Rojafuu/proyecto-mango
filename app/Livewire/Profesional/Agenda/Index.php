<?php

namespace App\Livewire\Profesional\Agenda;

use App\Models\Reserva;
use App\Models\Profesional;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $fecha = '';
    public string $estado = ''; // pendiente | propuesta | confirmada | cancelada | completada | (vacío = todos)

    // Modal
    public bool $modalAbierto = false;
    public ?Reserva $reservaSeleccionada = null;

    // Campos propuesta profesional
    public string $nota_profesional = '';
    public ?int $precio_estimado = null;
    public ?int $duracion_estimada_bloques = null;
    public ?string $sugerencia_fecha_hora = null; // datetime-local

    public function mount(): void
    {
        $this->fecha = now()->format('Y-m-d');
    }

    private function profesionalId(): int
    {
        $profesional = Profesional::where('user_id', Auth::id())->first();

        if (!$profesional) {
            abort(403, 'Tu usuario no tiene perfil profesional.');
        }

        return (int) $profesional->id;
    }

    private function asegurarPropiedad(Reserva $reserva): void
    {
        if ((int) $reserva->profesional_id !== $this->profesionalId()) {
            abort(403, 'No puedes ver/modificar reservas de otro profesional.');
        }
    }

    public function verReserva(int $reservaId): void
    {
        $reserva = Reserva::with(['cliente.user', 'procedimiento'])
            ->findOrFail($reservaId);

        $this->asegurarPropiedad($reserva);

        $this->reservaSeleccionada = $reserva;

        // Cargar valores existentes (si ya había propuesta)
        $this->nota_profesional = (string) ($reserva->nota_profesional ?? '');
        $this->precio_estimado = $reserva->precio_estimado ? (int) $reserva->precio_estimado : null;
        $this->duracion_estimada_bloques = $reserva->duracion_estimada_bloques ? (int) $reserva->duracion_estimada_bloques : null;

        // Para input datetime-local: YYYY-MM-DDTHH:MM
        $this->sugerencia_fecha_hora = $reserva->sugerencia_fecha_hora
            ? Carbon::parse($reserva->sugerencia_fecha_hora)->format('Y-m-d\TH:i')
            : null;

        $this->modalAbierto = true;
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->reservaSeleccionada = null;

        $this->nota_profesional = '';
        $this->precio_estimado = null;
        $this->duracion_estimada_bloques = null;
        $this->sugerencia_fecha_hora = null;
    }

    public function guardarPropuesta(): void
    {
        if (!$this->reservaSeleccionada) {
            return;
        }

        $reserva = Reserva::findOrFail($this->reservaSeleccionada->id);
        $this->asegurarPropiedad($reserva);

        // Validaciones simples (sin complicar)
        $this->validate([
            'nota_profesional' => 'nullable|string|max:2000',
            'precio_estimado' => 'nullable|integer|min:0',
            'duracion_estimada_bloques' => 'nullable|integer|min:1|max:20',
            'sugerencia_fecha_hora' => 'nullable|date',
        ]);

        $reserva->update([
            'nota_profesional' => $this->nota_profesional ?: null,
            'precio_estimado' => $this->precio_estimado,
            'duracion_estimada_bloques' => $this->duracion_estimada_bloques,
            'sugerencia_fecha_hora' => $this->sugerencia_fecha_hora ? Carbon::parse($this->sugerencia_fecha_hora) : null,
            'estado' => 'propuesta',
        ]);

        // Recargar reserva seleccionada para mostrar datos actualizados
        $this->reservaSeleccionada = $reserva->fresh(['cliente.user', 'procedimiento']);

        session()->flash('status_ok', 'Propuesta guardada. Estado actualizado a "propuesta".');
        $this->modalAbierto = false;
    }

    // Acciones posteriores
    public function confirmar(int $reservaId): void
    {
        $reserva = Reserva::findOrFail($reservaId);
        $this->asegurarPropiedad($reserva);

        // Confirmar solo si ya hubo propuesta (o si quieres, también desde pendiente)
        if (!in_array($reserva->estado, ['propuesta'], true)) {
            session()->flash('status', 'Solo puedes confirmar reservas en estado "propuesta".');
            return;
        }

        $reserva->update(['estado' => 'confirmada']);
        session()->flash('status_ok', 'Reserva confirmada.');
    }

    public function cancelar(int $reservaId): void
    {
        $reserva = Reserva::findOrFail($reservaId);
        $this->asegurarPropiedad($reserva);

        if (!in_array($reserva->estado, ['pendiente', 'propuesta', 'confirmada'], true)) {
            session()->flash('status', 'Solo puedes cancelar reservas pendientes, propuestas o confirmadas.');
            return;
        }

        $reserva->update(['estado' => 'cancelada']);
        session()->flash('status_ok', 'Reserva cancelada.');
    }

    public function completar(int $reservaId): void
    {
        $reserva = Reserva::findOrFail($reservaId);
        $this->asegurarPropiedad($reserva);

        if ($reserva->estado !== 'confirmada') {
            session()->flash('status', 'Solo puedes completar reservas confirmadas.');
            return;
        }

        $reserva->update(['estado' => 'completada']);
        session()->flash('status_ok', 'Reserva marcada como completada.');
    }

    public function render()
    {
        $profesionalId = $this->profesionalId();

        $query = Reserva::query()
            ->with(['cliente.user', 'procedimiento'])
            ->where('profesional_id', $profesionalId)
            ->orderBy('inicio');

        if ($this->fecha !== '') {
            $inicioDia = Carbon::parse($this->fecha)->startOfDay();
            $finDia = Carbon::parse($this->fecha)->endOfDay();
            $query->whereBetween('inicio', [$inicioDia, $finDia]);
        }

        if ($this->estado !== '') {
            $query->where('estado', $this->estado);
        }

        $reservas = $query->get();

        return view('livewire.profesional.agenda.index', compact('reservas'));
    }
}
