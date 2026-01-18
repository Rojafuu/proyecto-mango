<div class="container">
    <h1>Mis bloqueos</h1>

    @if($sinPerfil)
        <div class="alert alert-error">Tu usuario no tiene perfil de profesional.</div>
    @endif

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
            <h2 style="margin:0;">Listado</h2>
            <a class="btn" href="{{ route('profesional.bloqueos.crear') }}">+ Crear bloqueo</a>
        </div>

        @if($bloqueos->isEmpty())
            <p>No tienes bloqueos registrados.</p>
        @else
            <div class="tablewrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($bloqueos as $b)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($b->inicio)->format('d-m-Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($b->fin)->format('d-m-Y H:i') }}</td>
                                <td>{{ $b->motivo ? $b->motivo : '—' }}</td>

                                <td>
                                    @if($b->estado)
                                        <span class="badge badge-ok">Activo</span>
                                    @else
                                        <span class="badge badge-off">Inactivo</span>
                                    @endif
                                </td>

                                <td>
                                    <button type="button" class="btn"
                                            onclick="confirmEliminar({{ $b->id }})">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    </div>

    <script>
        function confirmEliminar(id) {
            Swal.fire({
                icon: 'warning',
                title: '¿Eliminar bloqueo?',
                text: 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('eliminarConfirmado', id);
                }
            });
        }
    </script>
</div>
