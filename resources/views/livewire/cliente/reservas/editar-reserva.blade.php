<div class="container">
    <h1>Editar reserva</h1>

    <div class="card">
        <form wire:submit.prevent="guardar">

            <div class="field">
                <label>Tipo</label>
                <select wire:model="tipo">
                    <option value="">-- Selecciona --</option>
                    <option value="tatuaje">Tatuaje</option>
                    <option value="bodypiercing">Body Piercing</option>
                </select>
                @error('tipo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Profesional</label>
                <select wire:model="profesional_id">
                    <option value="">-- Selecciona --</option>
                    @foreach($profesionales as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->user->nombre_completo ?? ('Profesional #' . $p->id) }}
                        </option>
                    @endforeach
                </select>
                @error('profesional_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Inicio</label>
                <input type="datetime-local" wire:model="inicio">
                @error('inicio') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Bloques (90 min c/u)</label>
                <select wire:model="bloques">
                    <option value="1">1 (90 min)</option>
                    <option value="2">2 (180 min)</option>
                    <option value="3">3 (270 min)</option>
                </select>
                @error('bloques') <div class="error">{{ $message }}</div> @enderror
            </div>

            @if($finPreview)
                <div class="alert alert-ok">
                    Fin estimado: <strong>{{ $finPreview }}</strong>
                </div>
            @endif

            <div class="field">
                <label>Observaciones</label>
                <input type="text" wire:model="observaciones" placeholder="Opcional">
                @error('observaciones') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button class="btn" type="submit">Guardar cambios</button>
            <a class="btn btn-outline" href="{{ route('cliente.reservas.index') }}">Volver</a>

        </form>
    </div>
</div>
