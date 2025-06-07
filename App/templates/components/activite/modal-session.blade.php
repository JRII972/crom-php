<!-- Modal Ajouter une session -->
  <dialog id="modal-ajouter-session" class="modal">
    <div class="modal-box max-w-md">
      <h3 class="font-bold text-lg mb-4">Ajouter une session</h3>

      <form id="form-ajouter-session" class="space-y-4">
        <!-- Titre de la session -->
        <div>
          <label class="label">
            <span class="label-text font-semibold">Titre de la session *</span>
          </label>
          <input type="text" id="session-titre" name="titre" class="input input-bordered w-full"
            placeholder="Ex: Session découverte, Chapitre 1, Événement spécial..." required maxlength="100">
          <label class="label">
            <span class="label-text-alt">Maximum 100 caractères</span>
          </label>
        </div>

        <!-- Sélection de la date -->
        <div>
          <label class="label">
            <span class="label-text font-semibold">Date de la session *</span>
          </label>
          <input type="date" id="session-date" name="date" class="input input-bordered w-full" required>
          <label class="label">
            <span class="label-text-alt text-info">Sélectionnez d'abord une date pour activer le choix du lieu</span>
          </label>
        </div>

        <!-- Sélection du lieu -->
        <div>
          <label class="label">
            <span class="label-text font-semibold">Lieu de la session *</span>
          </label>
          <select id="session-lieu" name="lieu" class="w-full" disabled required>
            <option value="">Sélectionnez d'abord une date...</option>
          </select>
          <label class="label">
            <span class="label-text-alt text-base-content/70">Le lieu détermine automatiquement l'heure de la
              session</span>
          </label>
        </div>

        <!-- Affichage de l'heure automatique -->
        <div id="session-heure-container" class="hidden">
          <label class="label">
            <span class="label-text font-semibold">Heure de la session</span>
          </label>
          <div class="input input-bordered w-full bg-base-200 flex items-center">
            <span id="session-heure-display" class="text-base-content/70">--:-- - --:--</span>
            <span class="text-xs text-base-content/50 ml-auto">Automatique selon le lieu</span>
          </div>
        </div>

        <!-- Nombre de joueurs max pour cette session -->
        <div>
          <label class="label">
            <span class="label-text font-semibold">Nombre maximum de joueurs</span>
          </label>
          <input type="number" id="session-max-joueurs" name="max_joueurs" class="input input-bordered w-full" min="1"
            max="8" value="5">
          <label class="label">
            <span class="label-text-alt">Par défaut basé sur la capacité de l'activité</span>
          </label>
        </div>

        <!-- Notes optionnelles -->
        <div>
          <label class="label">
            <span class="label-text font-semibold">Notes (optionnel)</span>
          </label>
          <textarea id="session-notes" name="notes" class="textarea textarea-bordered w-full h-20"
            placeholder="Informations spécifiques à cette session..."></textarea>
        </div>
      </form>
      <div class="modal-action">
        <form method="dialog">
          <button class="btn" type="button"
            onclick="document.getElementById('modal-ajouter-session').close()">Annuler</button>
        </form>
        <button type="submit" form="form-ajouter-session" class="btn btn-primary" id="btn-confirmer-session">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
          Créer la session
        </button>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button type="button">Fermer</button>
    </form>
  </dialog> <!-- Selectize.js pour le sélecteur de lieu -->
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>

  <!-- Styles personnalisés pour intégrer Selectize.js avec daisyUI -->
  <style>
    .selectize-control .selectize-input {
      height: auto !important;
      min-height: 3rem;
      padding: 0.75rem !important;
      border-radius: 0.5rem !important;
      border: 1px solid hsl(var(--bc) / 0.2);
      background-color: hsl(var(--b1));
      color: hsl(var(--bc));
    }

    .selectize-control .selectize-input.focus {
      border-color: hsl(var(--p));
      box-shadow: none !important;
    }

    .selectize-dropdown {
      background-color: hsl(var(--b1));
      border: 1px solid hsl(var(--bc) / 0.2);
      border-radius: 0.5rem !important;
      box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .selectize-dropdown .option {
      padding: 0.75rem !important;
      border-bottom: 1px solid hsl(var(--bc) / 0.1);
      color: hsl(var(--bc));
    }

    .selectize-dropdown .option:hover {
      background-color: hsl(var(--b2));
    }

    .selectize-dropdown .option.active {
      background-color: hsl(var(--p));
      color: hsl(var(--pc));
    }

    .selectize-control.single .selectize-input:after {
      border-color: hsl(var(--bc) / 0.5) transparent transparent transparent !important;
    }

    .selectize-control .selectize-input input {
      color: hsl(var(--bc)) !important;
    }

    .selectize-control .selectize-input input::placeholder {
      color: hsl(var(--bc) / 0.5) !important;
    }

    .selectize-control.disabled .selectize-input {
      background-color: hsl(var(--b2));
      color: hsl(var(--bc) / 0.5);
      opacity: 0.6;
    }
  </style>