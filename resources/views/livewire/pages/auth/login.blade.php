<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();

        if ($user->hasRole('administrador')) {
        $to = '/administrador';
        } elseif ($user->hasRole('profesional')) {
        $to = '/profesional';
        } else {
        $to = '/cliente';
        }

        $this->redirectIntended(default: $to, navigate: true);        
    }
};

?>

<div class="auth-wrap">
  <div class="auth-card">
    @if (session('status'))
      <div class="alert alert-ok">{{ session('status') }}</div>
    @endif

    <form wire:submit="login">
      <div>
        <label for="email">Email</label>
        <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username">
        @error('form.email') <span class="error">{{ $message }}</span> @enderror
      </div>

      <div style="margin-top:14px;">
        <label for="password">Contraseña</label>
        <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password">
        @error('form.password') <span class="error">{{ $message }}</span> @enderror
      </div>

      <div style="margin-top:14px;">
        <label>
          <input wire:model="form.remember" id="remember" type="checkbox" name="remember" style="width:auto; margin-right:8px;">
          Recordarme
        </label>
      </div>

      <div style="margin-top:16px; display:flex; gap:12px; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        @if (Route::has('password.request'))
          <a class="brand__link" href="{{ route('password.request') }}" wire:navigate>¿Olvidaste tu contraseña?</a>
        @endif

        <button type="submit">Ingresar</button>
      </div>
    </form>
  </div>
</div>
