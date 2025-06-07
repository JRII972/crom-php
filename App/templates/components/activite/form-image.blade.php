{{-- Image de la activite --}}
<div class="card p-3 bg-base-100 shadow-lg">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-image text-primary"></i>
            Image de la activite
        </h2>
    </div>
    <div class="card-body space-y-4">
        {{-- Choix du type d'image --}}
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Source de l'image</span>
            </label>
            <div class="join">
                <input type="radio" name="image_source" value="game" class="join-item btn btn-sm" id="tab-game" aria-label="Image du jeu" checked>
                <input type="radio" name="image_source" value="url" class="join-item btn btn-sm" aria-label="Via URL" id="tab-url">
                <input type="radio" name="image_source" value="upload" class="join-item btn btn-sm" aria-label="Fichier" id="tab-upload">
            </div>
        </div>
        {{-- URL personnalisée --}}
        <div class="form-control" id="url-input-container" style="display: none;">
            <label class="label">
                <span class="label-text font-semibold">URL de l'image</span>
            </label>
            <input type="url" id="image_url" name="image_url" class="input input-bordered" placeholder="https://exemple.com/image.jpg">
        </div>
        {{-- Upload de fichier --}}
        <div class="form-control" id="upload-input-container" style="display: none;">
            <label class="label">
                <span class="label-text font-semibold">Fichier image</span>
            </label>
            <input type="file" id="image_file" name="image_file" class="file-input file-input-bordered" accept="image/*">
        </div>
        {{-- Texte alternatif --}}
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Texte alternatif</span>
            </label>
            <input type="text" id="texte_alt_image" name="texte_alt_image" class="input input-bordered" maxlength="255" placeholder="Description de l'image pour l'accessibilité">
        </div>
        {{-- Aperçu de l'image --}}
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Aperçu</span>
            </label>
            <div class="border border-base-300 rounded-lg p-4 bg-base-50">
                <div id="image-preview" class="flex justify-center">
                    <div class="text-base-content/60">
                        <i class="fas fa-image text-4xl"></i>
                        <p class="mt-2">Aucune image sélectionnée</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
