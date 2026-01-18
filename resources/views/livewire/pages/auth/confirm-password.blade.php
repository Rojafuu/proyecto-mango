<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="auth-wrap">
  <div class="auth-card">

    <p class="hint">
      {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form wire:submit="confirmPassword">
      <div>
        <label for="password">Contraseña</label>

        <input
          wire:model="password"
          id="password"
          type="password"
          name="password"
          required
          autocomplete="current-password"
        >

        @error('password')
          <span class="error">{{ $message }}</span>
        @enderror
      </div>

      <div style="margin-top:16px; display:flex; justify-content:flex-end;">
        <button type="submit">Confirmar</button>
      </div>
    </form>

  </div>
</div>
