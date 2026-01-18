<?php

namespace App\Livewire\Cliente;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Inicio extends Component
{
    public function render()
    {
        return view('livewire.cliente.inicio');
    }
}
