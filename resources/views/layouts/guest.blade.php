<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mango') }}</title>

    <link rel="stylesheet" href="{{ asset('css/mango.css') }}">
</head>
<body>

    <header class="topbar">
        <div class="container topbar__inner">
            <div class="brand">
                <a href="{{ url('/') }}" class="brand__link">Mango</a>
            </div>

            <nav class="nav">
                <a href="{{ url('/') }}">Inicio</a>
                <a href="{{ url('/login') }}">Ingresar</a>
                <a href="{{ url('/register') }}">Registrarse</a>
            </nav>
        </div>
    </header>

    <main class="container auth-wrap">
        <div class="auth-card">
            {{ $slot }}
        </div>
    </main>

</body>
</html>
