<?php

namespace App\Livewire\Cliente\Reservas;

use App\Models\BloqueoAgenda;
use App\Models\Cliente;
use App\Models\Procedimiento;
use App\Models\Profesional;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CrearReserva extends Component
{
    // PROPIEDADES 

    // Solicitud 
    public $tipo = ''; // tatuaje | bodypiercing
    public $zona_cuerpo = '';
    public $estilo = '';
    public $ancho_cm = '';
    public $alto_cm = '';
    public $descripcion = '';

    //Agendamiento
    public $profesional_id = '';
    public $inicio = ''; // datetime-local
    public $bloques = 1; // 1..3 (cada bloque = 90 min)
    public $observaciones = '';

    public $finPreview = null;


    public function updated($property): void
    {
        // Si cambia tipo, resetea profesional y limpia medidas si NO es tatuaje
        if ($property === 'tipo') {
            $this->profesional_id = '';

            if ($this->tipo !== 'tatuaje') {
                $this->ancho_cm = '';
                $this->alto_cm  = '';
            }

            $this->calcularFinPreview();
        }

        // Si cambia inicio o bloques, recalcula término estimado
        if (in_array($property, ['inicio', 'bloques'])) {
            $this->calcularFinPreview();
        }
    }


    private function calcularFinPreview(): void
    {
        $this->finPreview = null;
        if (!$this->inicio) return;

        $inicio = Carbon::parse($this->inicio);
        $fin = $inicio->copy()->addMinutes($this->duracionEnMinutos());

        $this->finPreview = $fin->format('Y-m-d H:i');
    }

    private function duracionEnMinutos(): int
    {
        $b = (int) $this->bloques;
        if (!in_array($b, [1, 2, 3])) $b = 1;
        return $b * 90;
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


    public function guardar()
    {
        // Reglas base
        $reglas = [
            // Solicitud
            'tipo' => ['required', 'in:tatuaje,bodypiercing'],
            'zona_cuerpo' => ['required', 'string', 'max:100'],
            'estilo' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['required', 'string', 'max:255'],

            // Agenda
            'profesional_id' => ['required', 'exists:profesionales,id'],
            'inicio' => ['required', 'date'],
            'bloques' => ['required', 'integer', 'in:1,2,3'],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ];

        // Reglas condicionales: medidas solo para tatuaje
        if ($this->tipo === 'tatuaje') {
            $reglas['ancho_cm'] = ['required', 'numeric', 'gt:0', 'max:500'];
            $reglas['alto_cm']  = ['required', 'numeric', 'gt:0', 'max:500'];
        } else {
            $reglas['ancho_cm'] = ['nullable', 'numeric', 'gt:0', 'max:500'];
            $reglas['alto_cm']  = ['nullable', 'numeric', 'gt:0', 'max:500'];
        }

        // Validar formulario
        $this->validate($reglas);

        // Validar cliente
        $cliente = Cliente::where('user_id', Auth::id())->first();
        if (!$cliente) {
            $this->addError('inicio', 'Tu usuario no tiene perfil de cliente asociado.');
            $this->swalError('Perfil faltante', 'Tu usuario no tiene perfil de cliente asociado.');
            return;
        }

        // Validar profesional activo
        $prof = Profesional::with('user')
            ->where('id', $this->profesional_id)
            ->where('estado', true)
            ->first();

        if (!$prof) {
            $this->addError('profesional_id', 'El profesional seleccionado no está disponible.');
            $this->swalError('Profesional no disponible', 'El profesional seleccionado no está activo o no existe.');
            return;
        }

        // Validar que especialidad coincida con tipo
        if (strtolower(trim($prof->especialidad)) !== strtolower(trim($this->tipo))) {
            $this->addError('profesional_id', 'Este profesional no corresponde al tipo seleccionado.');
            $this->swalError('Selección inválida', 'El profesional no corresponde al tipo seleccionado.');
            return;
        }

        // Calcular inicio/fin
        $inicio = Carbon::parse($this->inicio);
        $fin = $inicio->copy()->addMinutes($this->duracionEnMinutos());

        // No permitir reservas en el pasado
        if ($inicio->lessThan(Carbon::now())) {
            $this->addError('inicio', 'No puedes agendar en una fecha/hora pasada.');
            $this->swalError('Fecha inválida', 'No puedes agendar en una fecha/hora pasada.');
            return;
        }

        // Fin > inicio
        if ($fin->lessThanOrEqualTo($inicio)) {
            $this->addError('inicio', 'La hora de término debe ser mayor que la hora de inicio.');
            $this->swalError('Horario inválido', 'La hora de término debe ser mayor que la de inicio.');
            return;
        }

        // Bloqueos agenda 
        $hayBloqueo = BloqueoAgenda::where('profesional_id', $this->profesional_id)
            ->where('estado', true)
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('inicio', '<', $fin)
                  ->where('fin', '>', $inicio);
            })
            ->exists();

        if ($hayBloqueo) {
            $this->addError('inicio', 'El profesional tiene un bloqueo de agenda en ese horario.');
            $this->swalError('Horario bloqueado', 'El profesional tiene un bloqueo de agenda en ese horario.');
            return;
        }

        // Reservas existentes
        $hayReserva = Reserva::where('profesional_id', $this->profesional_id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where(function ($q) use ($inicio, $fin) {
                $q->where('inicio', '<', $fin)
                  ->where('fin', '>', $inicio);
            })
            ->exists();

        if ($hayReserva) {
            $this->addError('inicio', 'Ya existe una reserva en ese horario para este profesional.');
            $this->swalError('Horario no disponible', 'Ese bloque ya está tomado. Elige otra fecha/hora.');
            return;
        }

        // Crear procedimiento 
        $ancho = ($this->tipo === 'tatuaje' && $this->ancho_cm !== '') ? (float) $this->ancho_cm : null;
        $alto  = ($this->tipo === 'tatuaje' && $this->alto_cm !== '') ? (float) $this->alto_cm : null;

        $procedimiento = Procedimiento::create([
            'tipo' => $this->tipo,
            'zona_cuerpo' => $this->zona_cuerpo,
            'estilo' => $this->estilo ?: null,
            'ancho_cm' => $ancho,
            'alto_cm' => $alto,
            'descripcion' => $this->descripcion,
            'duracion_estimada' => $this->duracionEnMinutos(),
        ]);

        // Crear reserva
        Reserva::create([
            'cliente_id' => $cliente->id,
            'profesional_id' => $this->profesional_id,
            'procedimiento_id' => $procedimiento->id,
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => 'pendiente',
            'observaciones' => $this->observaciones,
        ]);

        // SweetAlert éxito
        $this->dispatch('swal',
            icon: 'success',
            title: 'Solicitud enviada',
            text: 'Tu reserva quedó PENDIENTE de confirmación.',
            confirmButtonText: 'Ver mis reservas',
            redirect: route('cliente.reservas.index'),
        );


        
        // Reset form
        $this->reset([
            'tipo', 'zona_cuerpo', 'estilo', 'ancho_cm', 'alto_cm', 'descripcion',
            'profesional_id', 'inicio', 'bloques', 'observaciones', 'finPreview'
        ]);

        $this->bloques = 1;
    }

    //  RENDER 
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

        return view('livewire.cliente.reservas.crear-reserva', [
            'profesionales' => $profesionales,
        ]);
    }
}
