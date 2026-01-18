<?php

namespace App\Livewire\Profesional\Bloqueos;

use App\Models\BloqueoAgenda;
use App\Models\Profesional;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
  
    public function eliminarConfirmado(int $id): void
    {
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

        $bloqueo = BloqueoAgenda::where('id', $id)
            ->where('profesional_id', $profesional->id)
            ->first();

        if (!$bloqueo) {
            $this->dispatch('swal',
                icon: 'error',
                title: 'No encontrado',
                text: 'No se encontró el bloqueo o no te pertenece.',
                confirmButtonText: 'OK',
            );
            return;
        }

        // ELIMINAR 
        $bloqueo->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Bloqueo eliminado',
            text: 'Se eliminó correctamente.',
            confirmButtonText: 'OK',
        );
    }

    public function render()
    {
        $profesional = Profesional::where('user_id', Auth::id())->first();

        if (!$profesional) {
            return view('livewire.profesional.bloqueos.index', [
                'bloqueos' => collect(),
                'sinPerfil' => true,
            ]);
        }

        $bloqueos = BloqueoAgenda::where('profesional_id', $profesional->id)
            ->orderByDesc('inicio')
            ->get();

        return view('livewire.profesional.bloqueos.index', [
            'bloqueos' => $bloqueos,
            'sinPerfil' => false,
        ]);
    }
}
