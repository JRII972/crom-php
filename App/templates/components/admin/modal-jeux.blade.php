<dialog id="modal_add_game" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Ajouter un nouveau jeu</h3>

      <form class="space-y-4">
        <label class="form-control">
          <div class="label">
            <span class="label-text">Nom du jeu</span>
          </div>
          <input type="text" placeholder="Nom du jeu" class="input input-bordered" required />
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Genre</span>
          </div>
          <select class="select select-bordered" required>
            <option disabled selected>Choisir un genre</option>
            <option value="strategie">Stratégie</option>
            <option value="familial">Familial</option>
            <option value="cooperatif">Coopératif</option>
            <option value="party">Party Game</option>
          </select>
        </label>

        <div class="grid grid-cols-2 gap-4">
          <label class="form-control">
            <div class="label">
              <span class="label-text">Joueurs min</span>
            </div>
            <input type="number" placeholder="2" class="input input-bordered" min="1" />
          </label>

          <label class="form-control">
            <div class="label">
              <span class="label-text">Joueurs max</span>
            </div>
            <input type="number" placeholder="4" class="input input-bordered" min="1" />
          </label>
        </div>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Durée (minutes)</span>
          </div>
          <input type="number" placeholder="60" class="input input-bordered" min="1" />
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Difficulté</span>
          </div>
          <select class="select select-bordered" required>
            <option disabled selected>Choisir la difficulté</option>
            <option value="facile">Facile</option>
            <option value="moyen">Moyen</option>
            <option value="difficile">Difficile</option>
          </select>
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Image du jeu</span>
          </div>
          <input type="file" class="file-input file-input-bordered" accept="image/*" />
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Description</span>
          </div>
          <textarea class="textarea textarea-bordered" placeholder="Description du jeu..."></textarea>
        </label>
      </form>

      <div class="modal-action">
        <form method="dialog">
          <button type="button" class="btn">Annuler</button>
        </form>
        <button type="button" class="btn btn-primary">Ajouter</button>
      </div>
    </div>
  </dialog>