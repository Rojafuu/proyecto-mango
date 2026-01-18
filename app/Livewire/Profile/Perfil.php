<?php

namespace App\Livewire\Profile;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Perfil extends Component
{
    public function render()
    {
        return view('livewire.profile.perfil');
    }
}
