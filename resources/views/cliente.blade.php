@extends('layouts.app')

@section('content')
  <div class="container">
    <h1>Panel Cliente</h1>

    <div class="card">
      <h2>Accesos rápidos</h2>
      <ul>
        <li><a href="{{ route('cliente.reservas.crear') }}">Crear reserva</a></li>
        <li><a href="{{ route('cliente.reservas.index') }}">Mis reservas</a></li>
      </ul>
    </div>
  </div>
@endsection
