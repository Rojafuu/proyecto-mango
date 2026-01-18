<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Dashboard;
use App\Livewire\Profile\Perfil;

use App\Livewire\Cliente\Inicio as ClienteInicio;
use App\Livewire\Cliente\Reservas\CrearReserva;
use App\Livewire\Cliente\Reservas\MisReservas;
use App\Livewire\Cliente\Reservas\EditarReserva;

use App\Livewire\Profesional\Inicio as ProfesionalInicio;
use App\Livewire\Profesional\Bloqueos\Crear as ProfesionalBloqueoCrear;
use App\Livewire\Profesional\Bloqueos\Index as ProfesionalBloqueosIndex;
use App\Livewire\Profesional\Agenda\Index as ProfesionalAgendaIndex;


use App\Livewire\Administrador\Inicio as AdminInicio;

Route::view('/', 'welcome');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('profile', Perfil::class)
    ->middleware(['auth'])
    ->name('profile');

//Cliente
Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/cliente', ClienteInicio::class)->name('cliente');

    Route::get('/cliente/reservas', MisReservas::class)
        ->name('cliente.reservas.index');

    Route::get('/cliente/reservas/crear', CrearReserva::class)
        ->name('cliente.reservas.crear');

    Route::get('/cliente/reservas/{reserva}/editar', EditarReserva::class)
        ->name('cliente.reservas.editar');
});


//Profesional
Route::middleware(['auth', 'role:profesional'])->group(function () {
    Route::get('/profesional', ProfesionalInicio::class)->name('profesional');

    Route::get('/profesional/bloqueos', ProfesionalBloqueosIndex::class)
        ->name('profesional.bloqueos.index');

    Route::get('/profesional/bloqueos/crear', ProfesionalBloqueoCrear::class)
        ->name('profesional.bloqueos.crear');

    Route::get('/profesional/agenda', ProfesionalAgendaIndex::class)
        ->name('profesional.agenda.index');

});

//Administrador
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/administrador', AdminInicio::class)
        ->name('administrador');
});



//Redirección según rol 
Route::get('/home', function () {
    $user = auth()->user();

    if ($user->hasRole('administrador')) return redirect()->route('administrador');
    if ($user->hasRole('profesional')) return redirect()->route('profesional');
    if ($user->hasRole('cliente')) return redirect()->route('cliente');

    auth()->logout();

    return redirect('/login')->with('status', 'Tu usuario no tiene rol asignado.');
})->middleware(['auth'])->name('home');

require __DIR__ . '/auth.php';
