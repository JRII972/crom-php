<div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
      <div class="card bg-base-200 shadow-xl">
        <div class="card-body">
          <h2 class="card-title text-2xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            Gestion des lieux
          </h2>

          <div class="flex justify-between items-center mb-6">
            <p class="text-base-content/70">Gérez les lieux où se déroulent les sessions de jeu.</p>
            <div class="flex gap-2">
              <label class="input input-bordered input-sm flex items-center gap-2">
                <input type="text" placeholder="Rechercher un lieu..." />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="w-4 h-4 opacity-70">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
              </label>
              <button type="button" class="btn btn-primary btn-sm" onclick="modal_add_location.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Ajouter un lieu
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th>Lieu</th>
                  <th>Adresse</th>
                  <th>Capacité</th>
                  <th>Type</th>
                  <th>Équipements</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div>
                      <div class="font-bold">Centre Culturel</div>
                      <div class="text-sm opacity-50">Salle polyvalente</div>
                    </div>
                  </td>
                  <td>123 Rue des Jeux, 75001 Paris</td>
                  <td><span class="badge badge-info">20 personnes</span></td>
                  <td><span class="badge badge-outline">Culturel</span></td>
                  <td>
                    <div class="flex gap-1">
                      <span class="badge badge-xs badge-success">Parking</span>
                      <span class="badge badge-xs badge-success">PMR</span>
                    </div>
                  </td>
                  <td>
                    <div class="flex gap-2">
                      <button type="button" class="btn btn-soft btn-info btn-xs">Modifier</button>
                      <button type="button" class="btn btn-soft btn-error btn-xs">Supprimer</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>