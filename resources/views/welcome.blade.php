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
            @auth
                <a href="{{ route('home') }}">Mi panel</a>

                <form method="POST" action="{{ url('/logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="linklike">Salir</button>
                </form>
            @else
                <a href="{{ url('/login') }}">Ingresar</a>
                <a href="{{ url('/register') }}">Registrarse</a>
            @endauth
        </nav>
    </div>
</header>

<main class="container content">
    <section class="hero">
        <h1>Mango</h1>
        <p class="muted">
            Información y educación sobre body piercing: cuidados, salud, materiales y recomendaciones.
        </p>

        @guest
            <div class="actions">
                <a class="btn" href="{{ url('/register') }}">Registrarse (Cliente)</a>
                <a class="btn btn-outline" href="{{ url('/login') }}">Ingresar</a>
            </div>
        @endguest
    </section>

    <section class="cards">
        <article class="card">
            <h2>Cuidados post piercing</h2>
            <p>Higiene, tiempos de cicatrización y qué evitar durante la recuperación.</p>
        </article>

        <article class="card">
            <h2>Materiales seguros</h2>
            <p>Titanio grado implante, acero quirúrgico, oro: pros y contras.</p>
        </article>

        <article class="card">
            <h2>Señales de alerta</h2>
            <p>Cuándo consultar: dolor persistente, secreción, fiebre, enrojecimiento extenso.</p>
        </article>
    </section>
</main>

</body>
</html>
