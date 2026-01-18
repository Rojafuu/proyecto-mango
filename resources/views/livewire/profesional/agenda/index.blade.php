<div class="container">
    <h1>Agenda (Profesional)</h1>

    @if (session('status_ok'))
        <div class="alert alert-ok">{{ session('status_ok') }}</div>
    @endif

    @if (session('status'))
        <div class="alert alert-error">{{ session('status') }}</div>
    @endif

    <form style="margin-bottom:12px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div style="min-width:220px; flex:1;">
                <label>Fecha</label>
                <input type="date" wire:model.live="fecha">
            </div>

            <div style="min-width:220px; flex:1;">
                <label>Estado</label>
                <select wire:model.live="estado">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="propuesta">Propuesta</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="completada">Completada</option>
                </select>
            </div>
        </div>

        <span class="hint">Tip: cambia la fecha/estado para filtrar tus reservas.</span>
    </form>

    @if ($reservas->isEmpty())
        <div class="alert">
            No hay reservas para los filtros seleccionados.
        </div>
    @else
        <div style="overflow:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Cliente</th>
                        <th>Procedimiento</th>
                        <th>Estado</th>
                        <th style="width:320px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservas as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->inicio)->format('d-m-Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->fin)->format('H:i') }}</td>
                            <td>{{ $r->cliente?->user?->name ?? '—' }}</td>
                            <td>@if($r->procedimiento)
                                {{ ucfirst($r->procedimiento->tipo) }} - {{ $r->procedimiento->zona_cuerpo }}
                                ({{ $r->procedimiento->duracion_estimada }} min)
                                @else — @endif </td>
                            <td>
                                @php
                                    $badge = match($r->estado) {
                                        'confirmada' => 'badge badge-ok',
                                        'completada' => 'badge badge-ok',
                                        'pendiente'  => 'badge badge-off',
                                        'propuesta'  => 'badge badge-off',
                                        'cancelada'  => 'badge badge-off',
                                        default      => 'badge badge-off'
                                    };
                                @endphp
                                <span class="{{ $badge }}">{{ $r->estado }}</span>
                            </td>
                            <td>
                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                    @if ($r->estado === 'pendiente')
                                        <button type="button" wire:click="verReserva({{ $r->id }})">Ver reserva</button>
                                        <button type="button" wire:click="cancelar({{ $r->id }})">Cancelar</button>
                                    @elseif ($r->estado === 'propuesta')
                                        <button type="button" wire:click="confirmar({{ $r->id }})">Confirmar</button>
                                        <button type="button" wire:click="cancelar({{ $r->id }})">Cancelar</button>
                                    @elseif ($r->estado === 'confirmada')
                                        <button type="button" wire:click="completar({{ $r->id }})">Completar</button>
                                        <button type="button" wire:click="cancelar({{ $r->id }})">Cancelar</button>
                                    @else
                                        <span class="hint">Sin acciones</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if ($r->observaciones)
                            <tr>
                                <td colspan="6">
                                    <strong>Obs cliente:</strong> {{ $r->observaciones }}
                                </td>
                            </tr>
                        @endif

                        @if ($r->estado !== 'pendiente' && ($r->nota_profesional || $r->precio_estimado || $r->duracion_estimada_bloques || $r->sugerencia_fecha_hora))
                            <tr>
                                <td colspan="6">
                                    <strong>Propuesta profesional:</strong>
                                    <div style="margin-top:6px;">
                                        @if ($r->nota_profesional)
                                            <div><strong>Nota:</strong> {{ $r->nota_profesional }}</div>
                                        @endif
                                        @if ($r->precio_estimado)
                                            <div><strong>Precio estimado:</strong> ${{ number_format($r->precio_estimado, 0, ',', '.') }}</div>
                                        @endif
                                        @if ($r->duracion_estimada_bloques)
                                            <div><strong>Duración estimada (bloques):</strong> {{ $r->duracion_estimada_bloques }}</div>
                                        @endif
                                        @if ($r->sugerencia_fecha_hora)
                                            <div><strong>Sugerencia fecha/hora:</strong> {{ \Carbon\Carbon::parse($r->sugerencia_fecha_hora)->format('d-m-Y H:i') }}</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- MODAL (sin JS externo) --}}
    @if ($modalAbierto && $reservaSeleccionada)
        <div style="
            position:fixed; inset:0; background:rgba(0,0,0,.45);
            display:flex; align-items:center; justify-content:center;
            padding:16px; z-index:9999;
        ">
            <div style="
                width:100%; max-width:720px; background:#fff; border:1px solid #ddd;
                border-radius:12px; padding:16px;
            ">
                <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                    <div>
                        <h2 style="margin:0 0 8px 0;">Ver reserva</h2>
                        <span class="hint">Revisa la solicitud del cliente y registra tu propuesta.</span>
                    </div>

                    <button type="button" class="linklike" wire:click="cerrarModal">Cerrar ✕</button>
                </div>

                <div style="margin-top:12px; border-top:1px solid #eee; padding-top:12px;">
                    <div style="display:flex; flex-wrap:wrap; gap:12px;">
                        <div style="flex:1; min-width:240px;">
                            <strong>Cliente:</strong>
                            <div>{{ $reservaSeleccionada->cliente?->user?->name ?? '—' }}</div>
                        </div>

                        <div style="flex:1; min-width:240px;">
                            <strong>Procedimiento:</strong>
                            <div>
    @if($reservaSeleccionada->procedimiento)
        {{ ucfirst($reservaSeleccionada->procedimiento->tipo) }} - {{ $reservaSeleccionada->procedimiento->zona_cuerpo }}
        ({{ $reservaSeleccionada->procedimiento->duracion_estimada }} min)
        <div class="hint">
            <strong>Idea:</strong> {{ $reservaSeleccionada->procedimiento->descripcion }}
        </div>
    @else
        —
    @endif
</div>

                        </div>

                        <div style="flex:1; min-width:240px;">
                            <strong>Inicio:</strong>
                            <div>{{ \Carbon\Carbon::parse($reservaSeleccionada->inicio)->format('d-m-Y H:i') }}</div>
                        </div>

                        <div style="flex:1; min-width:240px;">
                            <strong>Fin:</strong>
                            <div>{{ \Carbon\Carbon::parse($reservaSeleccionada->fin)->format('d-m-Y H:i') }}</div>
                        </div>

                        <div style="flex:1; min-width:100%;">
                            <strong>Observaciones del cliente:</strong>
                            <div>{{ $reservaSeleccionada->observaciones ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="guardarPropuesta" style="margin-top:12px;">
                    <h3 style="margin-top:0;">Propuesta del profesional</h3>

                    <label>Nota profesional</label>
                    <textarea
                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; min-height:90px;"
                        wire:model.defer="nota_profesional"
                        placeholder="Ej: sugerencias, cuidados, cambios de tamaño/estilo, detalles del diseño..."
                    ></textarea>
                    @error('nota_profesional') <span class="error">{{ $message }}</span> @enderror

                    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:12px;">
                        <div style="flex:1; min-width:200px;">
                            <label>Precio estimado (CLP)</label>
                            <input type="number" min="0" wire:model.defer="precio_estimado" placeholder="Ej: 35000">
                            @error('precio_estimado') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div style="flex:1; min-width:200px;">
                            <label>Duración estimada (bloques)</label>
                            <input type="number" min="1" max="20" wire:model.defer="duracion_estimada_bloques" placeholder="Ej: 2">
                            @error('duracion_estimada_bloques') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div style="flex:1; min-width:240px;">
                            <label>Sugerencia fecha/hora</label>
                            <input type="datetime-local" wire:model.defer="sugerencia_fecha_hora">
                            @error('sugerencia_fecha_hora') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="actions">
                        <a href="#" class="linklike" wire:click.prevent="cerrarModal">Cancelar</a>
                        <button type="submit">Guardar propuesta</button>
                    </div>

                    <span class="hint">Al guardar, el estado pasa a <strong>propuesta</strong>.</span>
                </form>
            </div>
        </div>
    @endif
</div>
