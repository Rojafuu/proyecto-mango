@extends('layouts.app')

@section('content')
  <div class="container">
    <h1>Panel Profesional</h1>

    <div class="card">
      <h2>Accesos rápidos</h2>
      <ul>
        <li><a href="{{ route('profesional.bloqueos.crear') }}">Crear Bloqueo</a></li>
        <li><a href="{{ route('profesional.bloqueos.index') }}">Mis Bloqueos</a></li>
        <li><a href="{{ route('profesional.agenda.index') }}">Mi Agenda</a></li>
      </ul>
    </div>
  </div>
@endsection
