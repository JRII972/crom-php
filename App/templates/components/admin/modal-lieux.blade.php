<dialog id="modal_add_location" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg mb-4">Ajouter un nouveau lieu</h3>

      <form class="space-y-4">
        <label class="form-control">
          <div class="label">
            <span class="label-text">Nom du lieu</span>
          </div>
          <input type="text" placeholder="Nom du lieu" class="input input-bordered" required />
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Adresse</span>
          </div>
          <input type="text" placeholder="Adresse complète" class="input input-bordered" required />
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Capacité</span>
          </div>
          <input type="number" placeholder="Nombre de personnes" class="input input-bordered" min="1" />
        </label>

        <label class="form-control">
          <div class="label">
            <span class="label-text">Type de lieu</span>
          </div>
          <select class="select select-bordered" required>
            <option disabled selected>Choisir un type</option>
            <option value="associatif">Local associatif</option>
            <option value="culturel">Centre culturel</option>
            <option value="prive">Lieu privé</option>
            <option value="autre">Autre</option>
          </select>
        </label>

        <div class="form-control">
          <div class="label">
            <span class="label-text">Équipements disponibles</span>
          </div>
          <div class="flex flex-wrap gap-2">
            <label class="cursor-pointer label">
              <span class="label-text mr-2">Parking</span>
              <input type="checkbox" class="checkbox checkbox-primary" />
            </label>
            <label class="cursor-pointer label">
              <span class="label-text mr-2">Accessible PMR</span>
              <input type="checkbox" class="checkbox checkbox-primary" />
            </label>
            <label class="cursor-pointer label">
              <span class="label-text mr-2">Matériel fourni</span>
              <input type="checkbox" class="checkbox checkbox-primary" />
            </label>
          </div>
        </div>
      </form>

      <div class="modal-action">
        <form method="dialog">
          <button type="button" class="btn">Annuler</button>
        </form>
        <button type="button" class="btn btn-primary">Ajouter</button>
      </div>
    </div>
  </dialog>