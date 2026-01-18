<div class="container">
    <h1>Solicitud y Reserva</h1>

    <p>
        Completa la solicitud y elige un bloque horario.
        La reserva queda <strong>pendiente</strong> hasta confirmación del profesional.
    </p>

    @if (session()->has('ok'))
        <div class="alert alert-ok">
            {{ session('ok') }}
        </div>
    @endif

    <form wire:submit.prevent="guardar">

        {{-- ===================================================== --}}
        {{-- 1) SOLICITUD / BRIEF --}}
        {{-- ===================================================== --}}
        <h3 style="margin-top:18px;">1) Solicitud</h3>

        {{-- Tipo --}}
        <div style="margin-bottom:12px;">
            <label for="tipo">Tipo</label>
            <select id="tipo" wire:model="tipo" wire:change="$refresh">
                <option value="">-- Selecciona --</option>
                <option value="tatuaje">Tatuaje</option>
                <option value="bodypiercing">Bodypiercing</option>
            </select>
            @error('tipo') <small class="error">{{ $message }}</small> @enderror
        </div>

        {{-- Zona --}}
        <div style="margin-bottom:12px;">
            <label for="zona_cuerpo">Zona del cuerpo</label>
            <input id="zona_cuerpo" type="text" wire:model="zona_cuerpo" placeholder="Ej: brazo, pierna, oreja...">
            @error('zona_cuerpo') <small class="error">{{ $message }}</small> @enderror
        </div>

        {{-- Estilo --}}
        <div style="margin-bottom:12px;">
            <label for="estilo">Estilo (opcional)</label>
            <input id="estilo" type="text" wire:model="estilo" placeholder="Ej: linework, blackwork, tradicional...">
            @error('estilo') <small class="error">{{ $message }}</small> @enderror
        </div>

   @if($tipo === 'tatuaje')
    <div style="margin-bottom:12px; display:flex; gap:12px; align-items:end;">
        <div style="flex:1;">
            <label for="ancho_cm">Ancho (cm)</label>
            <input id="ancho_cm" type="number" step="0.1" wire:model="ancho_cm" placeholder="Ej: 5">
            @error('ancho_cm') <small class="error">{{ $message }}</small> @enderror
        </div>

        <div style="flex:1;">
            <label for="alto_cm">Alto (cm)</label>
            <input id="alto_cm" type="number" step="0.1" wire:model="alto_cm" placeholder="Ej: 8">
            @error('alto_cm') <small class="error">{{ $message }}</small> @enderror
        </div>
    </div>
@endif


        {{-- Descripción --}}
        <div style="margin-bottom:12px;">
            <label for="descripcion">Descripción / idea</label>
            <input id="descripcion" type="text" wire:model="descripcion" placeholder="Describe tu idea (máx 255)">
            @error('descripcion') <small class="error">{{ $message }}</small> @enderror
        </div>


        {{-- ===================================================== --}}
        {{-- 2) AGENDAMIENTO --}}
        {{-- ===================================================== --}}
        <h3 style="margin-top:18px;">2) Agenda</h3>

        {{-- Profesional --}}
        <div style="margin-bottom:12px;">
            <label for="profesional">Profesional (según tipo)</label>

            <select id="profesional" wire:model="profesional_id">
                <option value="">
                    {{ $tipo ? '-- Selecciona --' : 'Selecciona primero el tipo' }}
                </option>

                @foreach ($profesionales as $pro)
                    <option value="{{ $pro->id }}">
                        {{ $pro->user?->nombre_completo ?: ($pro->user?->name ?: ('Profesional #' . $pro->id)) }}
                    </option>
                @endforeach
            </select>

            @error('profesional_id') <small class="error">{{ $message }}</small> @enderror

            @if($tipo && $profesionales->isEmpty())
                <small class="error">No hay profesionales disponibles para {{ $tipo }}.</small>
            @endif
        </div>

        {{-- Bloques --}}
        <div style="margin-bottom:12px;">
            <label for="bloques">Bloques sugeridos</label>
            <select id="bloques" wire:model="bloques">
                <option value="1">1 bloque (90 min)</option>
                <option value="2">2 bloques (180 min)</option>
                <option value="3">3 bloques (270 min)</option>
            </select>
            @error('bloques') <small class="error">{{ $message }}</small> @enderror
        </div>

        {{-- Inicio + preview fin --}}
        <div style="margin-bottom:12px;">
            <label for="inicio">Fecha y hora (inicio)</label>
            <input id="inicio" type="datetime-local" wire:model="inicio">
            @error('inicio') <small class="error">{{ $message }}</small> @enderror

            @if ($finPreview)
                <small class="hint">Término estimado: <strong>{{ $finPreview }}</strong></small>
            @endif
        </div>

        {{-- Observaciones --}}
        <div style="margin-bottom:12px;">
            <label for="obs">Observaciones (opcional)</label>
            <input id="obs" type="text" wire:model="observaciones" placeholder="Ej: alergias, cuidados, notas...">
            @error('observaciones') <small class="error">{{ $message }}</small> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit">Enviar Solicitud</button>
    </form>
</div>
