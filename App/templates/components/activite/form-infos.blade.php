{{-- Informations générales de l'activité --}}
<div class="card p-3 bg-base-100 shadow-lg">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-info-circle text-primary"></i>
            Informations générales
        </h2>
    </div>
    <div class="card-body space-y-4">
        {{-- Nom de l'activité --}}
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Nom de la activite *</span>
            </label>
            <input type="text" id="nom" name="nom" class="input input-bordered" required maxlength="255" placeholder="Entrez le nom de votre activite">
            <label class="label">
                <span class="label-text-alt text-error" id="nom-error" style="display: none;"></span>
            </label>
        </div>
        {{-- Jeu --}}
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Jeu *</span>
            </label>
            <select id="jeu" name="id_jeu" required>
                <option value="">Tapez pour rechercher un jeu...</option>
            </select>
            <label class="label">
                <span class="label-text-alt">Commencez à taper pour voir les suggestions</span>
            </label>
        </div>
        {{-- Type de activite et campagne --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-semibold">Type de activite *</span>
                </label>
                <select id="type_activite" name="type_activite" class="select select-bordered" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="CAMPAGNE">Campagne</option>
                    <option value="ONESHOT">One-shot</option>
                    <option value="JEU_DE_SOCIETE">Jeu de société</option>
                    <option value="EVENEMENT">Événement</option>
                </select>
            </div>
            <div class="form-control" id="type-campagne-container" style="display: none;">
                <label class="label">
                    <span class="label-text font-semibold">Type de campagne</span>
                </label>
                <select id="type_campagne" name="type_campagne" class="select select-bordered">
                    <option value="">Sélectionnez un type</option>
                    <option value="OUVERTE">Ouverte</option>
                    <option value="FERMEE">Fermée</option>
                </select>
            </div>
        </div>
        {{-- Description courte --}}
        <div class="form-control flex flex-col">
            <label class="label">
                <span class="label-text font-semibold">Description courte</span>
                <span class="label-text-alt" id="desc-courte-count">0/140</span>
            </label>
            <textarea id="description_courte" name="description_courte" class="textarea textarea-bordered w-full" rows="2" maxlength="140" placeholder="Résumé en quelques mots de votre activite (140 caractères max)"></textarea>
            <label class="label">
                <span class="label-text-alt">Cette description apparaîtra sur les cartes de activites</span>
            </label>
        </div>
    </div>
</div>
