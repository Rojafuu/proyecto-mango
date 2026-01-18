<?php

namespace App\Livewire\Profesional;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Inicio extends Component
{
    public function render()
    {
        return view('livewire.profesional.inicio');
    }
}
