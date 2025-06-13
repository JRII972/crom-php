{{-- Configuration des joueurs --}}
<div class="card p-3 bg-base-100 shadow-lg">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-users text-primary"></i>
            Configuration des joueurs
        </h2>
    </div>
    <div class="card-body space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Nombre max de joueurs total --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Nombre maximum d'inscriptions (total)</span>
                </label>
                <input type="number" id="nombre_max_joueurs" name="nombre_max_joueurs" class="input input-bordered" min="0" max="50" value="0" placeholder="0 = illimité">
                <label class="label">
                    <span class="label-text-alt">Nombre total de joueurs pour toute l'activite (0 = illimité)</span>
                </label>
            </div>
            {{-- Nombre max de joueurs par session --}}
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Joueurs maximum par session *</span>
                </label>
                <input type="number" id="max_joueurs_session" name="max_joueurs_session" class="input input-bordered" min="1" max="20" value="5" required>
                <label class="label">
                    <span class="label-text-alt text-error" id="max-session-error" style="display: none;"></span>
                </label>
            </div>
        </div>
        {{-- Activite verrouillée --}}
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-4">
                <input type="checkbox" id="verrouille" name="verrouille" class="checkbox checkbox-primary">
                <div>
                    <span class="label-text font-semibold">Activite verrouillée</span>
                    <div class="label-text-alt">Les nouvelles inscriptions seront bloquées</div>
                </div>
            </label>
        </div>
    </div>
</div>
