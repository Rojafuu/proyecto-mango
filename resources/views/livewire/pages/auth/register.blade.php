<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $nombre = '';
    public string $apellido_paterno = '';
    public string $apellido_materno = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],

            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $nombreCompleto = trim(
            $validated['nombre'] . ' ' . $validated['apellido_paterno'] . ' ' . ($validated['apellido_materno'] ?? '')
        );

        $userData = [
            // Compatibilidad Laravel (name)
            'name' => $nombreCompleto,

            // Campos 1:1 documento
            'nombre' => $validated['nombre'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'] ?? null,

            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),

            // Estado activo por defecto
            'estado' => true,

            // Rol espejo (Spatie lo define “realmente” en el listener)
            'rol' => 'cliente',
        ];

        event(new Registered($user = User::create($userData)));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}; 

?>

<div class="auth">
    <form wire:submit="register" class="form">
        <div class="field">
            <label for="nombre" class="label">Nombre</label>
            <input
                wire:model="nombre"
                id="nombre"
                type="text"
                name="nombre"
                class="input"
                required
                autofocus
                autocomplete="given-name"
            >
            @error('nombre') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="apellido_paterno" class="label">Apellido paterno</label>
            <input
                wire:model="apellido_paterno"
                id="apellido_paterno"
                type="text"
                name="apellido_paterno"
                class="input"
                required
                autocomplete="family-name"
            >
            @error('apellido_paterno') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="apellido_materno" class="label">Apellido materno (opcional)</label>
            <input
                wire:model="apellido_materno"
                id="apellido_materno"
                type="text"
                name="apellido_materno"
                class="input"
                autocomplete="additional-name"
            >
            @error('apellido_materno') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email" class="label">Email</label>
            <input
                wire:model="email"
                id="email"
                type="email"
                name="email"
                class="input"
                required
                autocomplete="username"
            >
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password" class="label">Contraseña</label>
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                class="input"
                required
                autocomplete="new-password"
            >
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation" class="label">Confirmar contraseña</label>
            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="input"
                required
                autocomplete="new-password"
            >
            @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="actions">
            <a class="link" href="{{ route('login') }}" wire:navigate>Ya tengo cuenta</a>
            <button type="submit" class="btn">Registrarme</button>
        </div>
    </form>
</div>
