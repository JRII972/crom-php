{{-- Boutons d'action --}}
<div class="card p-3 bg-base-100 shadow-lg">
    <div class="card-body">
        <div class="flex flex-col sm:flex-row gap-4 justify-between">
            <div class="flex gap-2">
                <button type="button" id="preview-btn" class="btn btn-outline">
                    <i class="fas fa-eye"></i>
                    Aperçu
                </button>
                <button type="button" id="save-draft-btn" class="btn btn-outline">
                    <i class="fas fa-save"></i>
                    Sauvegarder brouillon
                </button>
            </div>
            <div class="flex gap-2">
                <a href="activites.html" class="btn btn-ghost">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i>
                    <span id="submit-text">Créer la activite</span>
                </button>
            </div>
        </div>
    </div>
</div>
