@extends('layouts.app')

@section('content')
  <div class="container">
    <h1>Mi Perfil</h1>

    <div class="card">
      <h2>Datos de cuenta</h2>
      <livewire:profile.update-profile-information-form />
    </div>

    <div class="card">
      <h2>Seguridad</h2>
      <livewire:profile.update-password-form />
    </div>

    <div class="card">
      <h2>Eliminar cuenta</h2>
      <livewire:profile.delete-user-form />
    </div>
  </div>
@endsection
