<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mango') }}</title>

    <link rel="stylesheet" href="{{ asset('css/mango.css') }}">
    @livewireStyles
</head>
<body>

<header class="topbar">
    <div class="container topbar__inner">
       <a class="brand brand__link" href="{{ auth()->check() ? route('home') : url('/') }}">Mango</a>


        <nav class="nav">
    <a href="{{ auth()->check() ? route('home') : url('/') }}">Home</a>

    @auth
        <a href="{{ route('profile') }}">Perfil</a>

        @role('cliente')
            <a href="{{ route('cliente.reservas.index') }}">Mis Reservas</a>
            <a href="{{ route('cliente.reservas.crear') }}">Agendar Cita</a>
        @endrole

        @role('profesional')
            <a href="{{ route('profesional') }}">Panel</a>
            <a href="{{ route('profesional.bloqueos.crear') }}">Crear Bloqueo</a>
            <a href="{{ route('profesional.bloqueos.index') }}">Mis Bloqueos</a>
            <a href="{{ route('profesional.agenda.index') }}">Mi Agenda</a>
        @endrole

        @role('administrador')
            <a href="{{ route('administrador') }}">Panel</a>
        @endrole

      <form method="POST" action="{{ route('logout') }}" class="form-plain">
    @csrf
    <button type="submit" class="linklike">Salir</button>
</form>


    @endauth

    @guest
        <a href="{{ route('login') }}">Ingresar</a>
        <a href="{{ route('register') }}">Registrarse</a>
    @endguest
</nav>

    </div>
</header>

<main class="container content">

    {{-- Mensajes clásicos --}}
    @if (session('ok'))
        <div class="alert alert-ok">{{ session('ok') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{ $slot }}
</main>

@livewireScripts

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('livewire:init', () => {

    function fireSwal(payload) {
        let data = payload;

        if (Array.isArray(data)) data = data[0] ?? {};
        if (data && data.detail) data = data.detail;

        Swal.fire({
            icon: data.icon ?? 'success',
            title: data.title ?? 'Listo',
            text: data.text ?? '',
            confirmButtonText: data.confirmButtonText ?? 'OK',
            showConfirmButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed && data.redirect) {
                window.location.href = data.redirect;
            }
        });
    }

    // Deja SOLO este listener (browser event)
    window.addEventListener('swal', (event) => fireSwal(event));
});
</script>




</body>
</html>
