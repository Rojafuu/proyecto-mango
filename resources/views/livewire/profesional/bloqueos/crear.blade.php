<div class="container">
    <h1>Crear bloqueo de agenda</h1>

    <div class="card">
        <form wire:submit.prevent="guardar">
            <div class="field">
                <label>Inicio</label>
                <input type="datetime-local" wire:model="inicio">
                @error('inicio') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Fin</label>
                <input type="datetime-local" wire:model="fin">
                @error('fin') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Motivo (opcional)</label>
                <input type="text" wire:model="motivo" placeholder="Ej: almuerzo, descanso, trámite">
                @error('motivo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button class="btn" type="submit">Guardar bloqueo</button>
        </form>
    </div>
</div>
