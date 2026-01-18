<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="auth-wrap">
  <div class="auth-card">

    <p class="hint">
      {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    @if (session('status'))
      <div class="alert alert-ok">{{ session('status') }}</div>
    @endif

    <form wire:submit="sendPasswordResetLink">
      <div>
        <label for="email">Email</label>
        <input
          wire:model="email"
          id="email"
          type="email"
          name="email"
          required
          autofocus
        >
        @error('email')
          <span class="error">{{ $message }}</span>
        @enderror
      </div>

      <div style="margin-top:16px; display:flex; justify-content:flex-end;">
        <button type="submit">Enviar link</button>
      </div>
    </form>

  </div>
</div>
