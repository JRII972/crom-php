<div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
      <div class="card bg-base-200 shadow-xl">
        <div class="card-body">
          <h2 class="card-title text-2xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            Gestion des utilisateurs
          </h2>

          <!-- Sous-tabs pour utilisateurs -->
          <div role="tablist" class="tabs tabs-box mb-6">
            <!-- Sous-tab: Liste des utilisateurs -->
            <input type="radio" name="users_tabs" role="tab" class="tab" aria-label="Liste des utilisateurs" checked="checked" />
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-4 h-4 opacity-70">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Liste des utilisateurs</h3>
                <div class="flex gap-2">
                  <label class="input input-bordered input-sm flex items-center gap-2">
                    <input type="text" placeholder="Rechercher..." />

                  </label>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="table table-zebra">
                  <thead>
                    <tr>
                      <th>Utilisateur</th>
                      <th>Email</th>
                      <th>Rôle</th>
                      <th>Date d'inscription</th>
                      <th>Statut</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="flex items-center gap-3">
                          <div class="avatar">
                            <div class="mask mask-squircle h-12 w-12">
                              <img src="https://picsum.photos/seed/user1/200" alt="Avatar de Marie Dupont" />
                            </div>
                          </div>
                          <div>
                            <div class="font-bold">Marie Dupont</div>
                            <div class="text-sm opacity-50">@marie_d</div>
                          </div>
                        </div>
                      </td>
                      <td>marie.dupont@example.com</td>
                      <td><span class="badge badge-primary">Utilisateur</span></td>
                      <td>15/05/2025</td>
                      <td><span class="badge badge-success">Actif</span></td>
                      <td>
                        <div class="flex gap-2">
                          <button type="button" class="btn btn-soft btn-info btn-xs">Modifier</button>
                          <button type="button" class="btn btn-soft btn-warning btn-xs">Suspendre</button>
                          <button type="button" class="btn btn-soft btn-error btn-xs">Supprimer</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div class="flex justify-center mt-6">
                <div class="join">
                  <button type="button" class="join-item btn btn-sm">«</button>
                  <button type="button" class="join-item btn btn-sm btn-active">1</button>
                  <button type="button" class="join-item btn btn-sm">2</button>
                  <button type="button" class="join-item btn btn-sm">3</button>
                  <button type="button" class="join-item btn btn-sm">»</button>
                </div>
              </div>
            </div>

            <!-- Sous-tab: Ajouter un utilisateur -->
            <input type="radio" name="users_tabs" role="tab" class="tab" aria-label="Ajouter un utilisateur" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
              <h3 class="text-xl font-bold mb-6">Ajouter un nouvel utilisateur</h3>

              <form class="space-y-4 max-w-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <label class="form-control">
                    <div class="label">
                      <span class="label-text">Prénom *</span>
                    </div>
                    <input type="text" placeholder="Prénom" class="input input-bordered" required />
                  </label>

                  <label class="form-control">
                    <div class="label">
                      <span class="label-text">Nom *</span>
                    </div>
                    <input type="text" placeholder="Nom" class="input input-bordered" required />
                  </label>
                </div>

                <label class="form-control">
                  <div class="label">
                    <span class="label-text">Email *</span>
                  </div>
                  <input type="email" placeholder="email@example.com" class="input input-bordered" required />
                </label>

                <label class="form-control">
                  <div class="label">
                    <span class="label-text">Nom d'utilisateur *</span>
                  </div>
                  <input type="text" placeholder="nom_utilisateur" class="input input-bordered" required />
                </label>

                <label class="form-control">
                  <div class="label">
                    <span class="label-text">Rôle</span>
                  </div>
                  <select class="select select-bordered" required>
                    <option disabled selected>Choisir un rôle</option>
                    <option value="user">Utilisateur</option>
                    <option value="moderator">Modérateur</option>
                    <option value="admin">Administrateur</option>
                  </select>
                </label>

                <div class="flex gap-4 pt-4">
                  <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                  <button type="reset" class="btn btn-ghost">Réinitialiser</button>
                </div>
              </form>
            </div>

            <!-- Sous-tab: Gestion des administrateurs -->
            <input type="radio" name="users_tabs" role="tab" class="tab" aria-label="Gestion des administrateurs" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
              <h3 class="text-xl font-bold mb-6">Gestion des administrateurs</h3>

              <div class="alert alert-info mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span>Gérez les droits d'administration de votre plateforme.</span>
              </div>

              <div class="overflow-x-auto">
                <table class="table table-zebra">
                  <thead>
                    <tr>
                      <th>Administrateur</th>
                      <th>Email</th>
                      <th>Dernière connexion</th>
                      <th>Permissions</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="flex items-center gap-3">
                          <div class="avatar">
                            <div class="mask mask-squircle h-12 w-12">
                              <img src="https://picsum.photos/seed/admin1/200" alt="Avatar d'Admin Super" />
                            </div>
                          </div>
                          <div>
                            <div class="font-bold">Admin Super</div>
                            <div class="text-sm opacity-50">Super administrateur</div>
                          </div>
                        </div>
                      </td>
                      <td>admin@lbdr.com</td>
                      <td>Aujourd'hui 14:30</td>
                      <td><span class="badge badge-error">Toutes</span></td>
                      <td>
                        <button type="button" class="btn btn-soft btn-info btn-xs">Modifier</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>