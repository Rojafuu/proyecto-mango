<div class="container">
    <h1>Mis Reservas</h1>

    <p style="margin-bottom:12px;">
        <a href="{{ route('cliente.reservas.crear') }}">+ Crear nueva reserva</a>
    </p>

    @if (!$clienteExiste)
        <div class="alert">
            Tu usuario no tiene perfil de cliente asociado.
        </div>
    @elseif ($reservas->isEmpty())
        <div class="alert">
            Aún no tienes reservas.
        </div>
    @else
        <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse; background:#fff; border:1px solid #ddd; border-radius:8px;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Inicio</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Fin</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Procedimiento</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Profesional</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Estado</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($reservas as $r)
                        <tr>
                            <td style="padding:10px; border-bottom:1px solid #eee;">
                                {{ \Carbon\Carbon::parse($r->inicio)->format('d-m-Y H:i') }}
                            </td>

                            <td style="padding:10px; border-bottom:1px solid #eee;">
                                {{ \Carbon\Carbon::parse($r->fin)->format('d-m-Y H:i') }}
                            </td>

                            <td style="padding:10px; border-bottom:1px solid #eee;">
                                @if ($r->procedimiento)
                                    {{ $r->procedimiento->tipo }} - {{ $r->procedimiento->zona_cuerpo ?? '' }}
                                    ({{ $r->procedimiento->duracion_estimada }} min)
                                @else
                                    —
                                @endif
                            </td>

                            <td style="padding:10px; border-bottom:1px solid #eee;">
                                {{ $r->profesional?->user?->nombre_completo ?? ('Profesional #' . $r->profesional_id) }}
                            </td>

                            <td style="padding:10px; border-bottom:1px solid #eee;">
                                {{ ucfirst($r->estado) }}
                            </td>

                            <td style="padding:10px; border-bottom:1px solid #eee;">
                                {{-- 1) Si está propuesta: mostrar botón Ver propuesta --}}
                                @if($r->estado === 'propuesta')
                                    <button type="button"
                                            style="padding:6px 10px; border:1px solid #333; background:#fff; color:#333; border-radius:6px; cursor:pointer;"
                                            wire:click="verPropuesta({{ $r->id }})">
                                        {{ ($verPropuestaId === $r->id) ? 'Ocultar' : 'Ver propuesta' }}
                                    </button>

                                {{-- 2) Si está pendiente o confirmada: editar/cancelar --}}
                                @elseif(in_array($r->estado, ['pendiente','confirmada']))
                                    <a href="{{ route('cliente.reservas.editar', $r->id) }}"
                                       style="padding:6px 10px; border:1px solid #333; background:#fff; color:#333; border-radius:6px; text-decoration:none; margin-right:6px; display:inline-block;">
                                        Editar
                                    </a>

                                    <button type="button"
                                            style="padding:6px 10px; border:1px solid #cc0000; background:#fff; color:#cc0000; border-radius:6px; cursor:pointer;"
                                            onclick="confirmCancelar({{ $r->id }})">
                                        Cancelar
                                    </button>
                                @else
                                    <span style="opacity:.6;">—</span>
                                @endif
                            </td>
                        </tr>

                        {{-- ✅ Mostrar detalle solo si: estado propuesta y está “abierto” --}}
                        @if ($r->estado === 'propuesta' && $verPropuestaId === $r->id)
                            <tr>
                                <td colspan="6" style="padding:12px 10px; border-bottom:1px solid #eee; background:#fafafa;">
                                    <strong>Propuesta del profesional</strong>

                                    <div style="margin-top:8px;">
                                        @if ($r->nota_profesional)
                                            <div><strong>Nota:</strong> {{ $r->nota_profesional }}</div>
                                        @endif

                                        @if ($r->precio_estimado)
                                            <div>
                                                <strong>Precio estimado:</strong>
                                                ${{ number_format($r->precio_estimado, 0, ',', '.') }}
                                            </div>
                                        @endif

                                        @if ($r->duracion_estimada_bloques)
                                            <div><strong>Duración estimada (bloques):</strong> {{ $r->duracion_estimada_bloques }}</div>
                                        @endif

                                        @if ($r->sugerencia_fecha_hora)
                                            <div>
                                                <strong>Sugerencia fecha/hora:</strong>
                                                {{ \Carbon\Carbon::parse($r->sugerencia_fecha_hora)->format('d-m-Y H:i') }}
                                            </div>
                                        @endif

                                        @if (!$r->nota_profesional && !$r->precio_estimado && !$r->duracion_estimada_bloques && !$r->sugerencia_fecha_hora)
                                            <span class="hint">
                                                El profesional marcó la reserva como propuesta, pero aún no registró detalles.
                                            </span>
                                        @endif
                                    </div>

                                    {{-- ✅ OPCIONAL: botón aceptar propuesta --}}
                                    <div style="margin-top:12px;">
                                        <button type="button"
                                                style="padding:8px 12px; border:1px solid #111; background:#111; color:#fff; border-radius:6px; cursor:pointer;"
                                                wire:click="aceptarPropuesta({{ $r->id }})">
                                            Aceptar propuesta
                                        </button>
                                    </div>
                                    {{-- Si NO quieres aceptar todavía: borra este div completo --}}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <script>
            function confirmCancelar(id) {
                Swal.fire({
                    icon: 'warning',
                    title: '¿Cancelar reserva?',
                    text: 'La reserva quedará como cancelada.',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'Volver',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('cancelarConfirmado', id);
                    }
                });
            }
        </script>
    @endif
</div>
