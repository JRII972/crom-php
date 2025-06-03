<!-- Page de profil -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              
              <!-- Colonne de gauche - Infos de base et photo -->
              <div class="col-span-1">
                <div class="card bg-base-200 shadow-xl overflow-hidden">
                  <div class="card-body items-center text-center relative pb-6">
                    <!-- Photo de profil avec badge pour édition -->
                    <div class="avatar mb-4 relative">
                      <div class="w-32 h-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img src="https://picsum.photos/200" alt="Photo de profil" id="profile-image" />
                      </div>
                      <button class="absolute bottom-0 right-0 btn btn-circle btn-sm btn-primary" id="change-photo-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                      </button>
                    </div>
                    
                    <!-- Nom et prénom -->
                    <h2 class="card-title text-2xl mb-1" id="profile-full-name">Thomas Dubois</h2>
                    
                    <!-- Pseudonyme si disponible -->
                    <p class="text-base-content/70 text-lg mb-3" id="profile-pseudo">@TomD</p>
                    
                    <!-- Type d'utilisateur -->
                    <div class="badge badge-accent mb-4" id="profile-type">Membre inscrit</div>
                    
                    <!-- Informations de base -->
                    <div class="w-full flex flex-col gap-2 text-left mt-2">
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <span id="profile-email">thomas@email.com</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span id="profile-username">login: tomdu92</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        <span id="profile-birthdate">Né le 15/04/1990 (35 ans)</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                        </svg>
                        <span id="profile-discord">Discord: TomD#1234</span>
                      </div>
                    </div>
                    
                    <!-- Bouton éditer profil -->
                    <button class="btn btn-primary btn-outline w-full mt-6" id="edit-profile-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21h-9.5A2.25 2.25 0 0 1 4 18.75V8.25A2.25 2.25 0 0 1 6.25 6H11" />
                      </svg>
                      Modifier mon profil
                    </button>
                  </div>
                </div>

                <!-- Statistiques -->
                <div class="card bg-base-200 shadow-xl mt-6">
                  <div class="card-body">
                    <h3 class="card-title text-xl mb-4">Statistiques</h3>
                    
                    <div class="stats stats-vertical shadow w-full">
                      <div class="stat">
                        <div class="stat-title">Membre depuis</div>
                        <div class="stat-value text-xl text-primary" id="profile-member-since">3 ans</div>
                        <div class="stat-desc" id="profile-member-date">Inscription le 15/06/2022</div>
                      </div>
                      
                      <div class="stat">
                        <div class="stat-title">Parties jouées</div>
                        <div class="stat-value text-xl text-secondary">42</div>
                        <div class="stat-desc">+8% par rapport à l'année dernière</div>
                      </div>
                      
                      <div class="stat">
                        <div class="stat-title">Parties créées</div>
                        <div class="stat-value text-xl text-accent">7</div>
                        <div class="stat-desc">En tant que maître de jeu</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Colonne centrale et droite - Tabs et contenus -->
              <div class="col-span-1 lg:col-span-2">
                <div class="card bg-base-200 shadow-xl h-full">
                  <div class="card-body">
                    <div role="tablist" class="tabs tabs-lifted">
                      <a role="tab" class="tab tab-active" id="tab-parties" aria-selected="true">Mes Parties</a>
                      <a role="tab" class="tab" id="tab-disponibilites">Disponibilités</a>
                      <a role="tab" class="tab" id="tab-historique">Historique</a>
                      <a role="tab" class="tab" id="tab-preference">Préférences</a>
                      <a role="tab" class="tab" id="tab-paiements">Paiements</a>
                    </div>
                    
                    <!-- Contenu du tab Mes Parties -->
                    <div id="tab-content-parties" class="py-4">
                      <h3 class="text-lg font-bold mb-4">Mes parties en cours</h3>
                      
                      <!-- Liste des parties -->
                      <div class="overflow-x-auto">
                        <table class="table table-zebra">
                          <thead>
                            <tr>
                              <th>Nom de la partie</th>
                              <th>Type</th>
                              <th>Rôle</th>
                              <th>Prochaine session</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>Les Ombres d'Esteren</td>
                              <td>
                                <div class="badge badge-primary">Campagne</div>
                              </td>
                              <td>Joueur</td>
                              <td>15 juin 2025</td>
                              <td>
                                <button class="btn btn-xs btn-outline">Voir</button>
                              </td>
                            </tr>
                            <tr>
                              <td>Donjons & Dragons</td>
                              <td>
                                <div class="badge badge-secondary">OneShot</div>
                              </td>
                              <td>Maître du jeu</td>
                              <td>22 juin 2025</td>
                              <td>
                                <button class="btn btn-xs btn-outline">Voir</button>
                              </td>
                            </tr>
                            <tr>
                              <td>Chroniques Oubliées</td>
                              <td>
                                <div class="badge badge-primary">Campagne</div>
                              </td>
                              <td>Joueur</td>
                              <td>29 juin 2025</td>
                              <td>
                                <button class="btn btn-xs btn-outline">Voir</button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <!-- Bouton créer une partie -->
                      <div class="mt-6 flex justify-end">
                        <button class="btn btn-primary">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                          </svg>
                          Créer une nouvelle partie
                        </button>
                      </div>
                    </div>
                    
                    <!-- Contenu du tab Disponibilités -->
                    <div id="tab-content-disponibilites" class="py-4 hidden">
                      <h3 class="text-lg font-bold mb-4">Mes disponibilités</h3>
                      
                      <!-- Calendrier des disponibilités -->
                      <div class="grid grid-cols-7 gap-2 mb-4">
                        <div class="text-center font-medium">Lun</div>
                        <div class="text-center font-medium">Mar</div>
                        <div class="text-center font-medium">Mer</div>
                        <div class="text-center font-medium">Jeu</div>
                        <div class="text-center font-medium">Ven</div>
                        <div class="text-center font-medium">Sam</div>
                        <div class="text-center font-medium">Dim</div>
                        
                        <!-- Exemple de jours avec disponibilités -->
                        <div class="btn btn-outline btn-sm">1</div>
                        <div class="btn btn-outline btn-sm">2</div>
                        <div class="btn btn-primary btn-sm">3</div>
                        <div class="btn btn-outline btn-sm">4</div>
                        <div class="btn btn-primary btn-sm">5</div>
                        <div class="btn btn-primary btn-sm">6</div>
                        <div class="btn btn-outline btn-sm">7</div>
                        <!-- Continuer pour le reste du mois... -->
                      </div>
                      
                      <div class="divider my-6">Disponibilités récurrentes</div>
                      
                      <!-- Liste des disponibilités -->
                      <div class="overflow-x-auto">
                        <table class="table table-zebra">
                          <thead>
                            <tr>
                              <th>Jour</th>
                              <th>Heure de début</th>
                              <th>Heure de fin</th>
                              <th>Type</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>Mercredi</td>
                              <td>19:00</td>
                              <td>23:00</td>
                              <td>
                                <div class="badge badge-success">Disponible</div>
                              </td>
                              <td>
                                <button class="btn btn-xs btn-outline btn-error">Supprimer</button>
                              </td>
                            </tr>
                            <tr>
                              <td>Vendredi</td>
                              <td>20:00</td>
                              <td>23:30</td>
                              <td>
                                <div class="badge badge-success">Disponible</div>
                              </td>
                              <td>
                                <button class="btn btn-xs btn-outline btn-error">Supprimer</button>
                              </td>
                            </tr>
                            <tr>
                              <td>Samedi</td>
                              <td>14:00</td>
                              <td>18:00</td>
                              <td>
                                <div class="badge badge-success">Disponible</div>
                              </td>
                              <td>
                                <button class="btn btn-xs btn-outline btn-error">Supprimer</button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <!-- Bouton ajouter disponibilité -->
                      <div class="mt-6 flex justify-end">
                        <button class="btn btn-primary">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                          </svg>
                          Ajouter une disponibilité
                        </button>
                      </div>
                    </div>
                    
                    <!-- Contenu du tab Historique -->
                    <div id="tab-content-historique" class="py-4 hidden">
                      <h3 class="text-lg font-bold mb-4">Historique des parties</h3>
                      
                      <!-- Filtres -->
                      <div class="flex flex-wrap gap-2 mb-6">
                        <select class="select select-bordered w-full max-w-xs">
                          <option disabled selected>Filtrer par type</option>
                          <option>Toutes les parties</option>
                          <option>Campagnes</option>
                          <option>OneShots</option>
                          <option>Jeux de société</option>
                        </select>
                        
                        <select class="select select-bordered w-full max-w-xs">
                          <option disabled selected>Filtrer par rôle</option>
                          <option>Tous les rôles</option>
                          <option>Maître du jeu</option>
                          <option>Joueur</option>
                        </select>
                      </div>
                      
                      <!-- Liste de l'historique -->
                      <div class="overflow-x-auto">
                        <table class="table table-zebra">
                          <thead>
                            <tr>
                              <th>Nom de la partie</th>
                              <th>Type</th>
                              <th>Date</th>
                              <th>Rôle</th>
                              <th>Lieu</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>Appel de Cthulhu</td>
                              <td>
                                <div class="badge badge-secondary">OneShot</div>
                              </td>
                              <td>15 mai 2025</td>
                              <td>Joueur</td>
                              <td>Salle Principale</td>
                            </tr>
                            <tr>
                              <td>Pathfinder</td>
                              <td>
                                <div class="badge badge-primary">Campagne</div>
                              </td>
                              <td>8 mai 2025</td>
                              <td>Maître du jeu</td>
                              <td>Salle 2</td>
                            </tr>
                            <tr>
                              <td>Dixit</td>
                              <td>
                                <div class="badge badge-accent">Jeu de société</div>
                              </td>
                              <td>1 mai 2025</td>
                              <td>Joueur</td>
                              <td>Salle 3</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    
                    <div id="tab-content-preference" class="py-4 hidden">
                        <!-- Préférences d'affichage des cartes -->
                        <div class="card bg-base-200 shadow-md mb-6 text-left">
                            <div class="card-body items-start">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                    </svg>
                                    Préférences d'affichage des cartes
                                </h2>
                                <p class="text-sm text-base-content/70 mb-4">Personnalisez l'affichage des cartes de parties et d'événements.</p>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Afficher les joueurs</h3>
                                        <p class="text-sm text-base-content/70">Montrer la liste des joueurs inscrits sur les cartes de parties</p>
                                    </div>
                                    <input type="checkbox" class="toggle toggle-primary" checked />
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Afficher les noms de parties</h3>
                                        <p class="text-sm text-base-content/70">Montrer les noms complets des parties plutôt que des descriptions courtes</p>
                                    </div>
                                    <input type="checkbox" class="toggle toggle-primary" checked />
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium">Afficher les pseudonymes</h3>
                                        <p class="text-sm text-base-content/70">Utiliser les pseudonymes au lieu des noms réels des utilisateurs</p>
                                    </div>
                                    <input type="checkbox" class="toggle toggle-primary" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <div class="card bg-base-200 shadow-md mb-6 text-left">
                            <div class="card-body">
                            <h2 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                Notifications et rappels
                            </h2>
                            <p class="text-sm text-base-content/70 mb-4">Gérez vos préférences de notifications et rappels pour les parties et événements.</p>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium">Afficher des informations personnalisées</h3>
                                    <p class="text-sm text-base-content/70">Recevoir des recommandations basées sur vos préférences et votre historique</p>
                                </div>
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                </div>
                                
                                <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium">Envoyer des notifications de rappel</h3>
                                    <p class="text-sm text-base-content/70">Recevoir des rappels pour vos parties à venir</p>
                                </div>
                                <input type="checkbox" class="toggle toggle-primary" checked />
                                </div>
                                
                                <div class="form-control w-full max-w-xs ml-auto">
                                <label class="label">
                                    <span class="label-text">Délai de rappel</span>
                                </label>
                                <select class="select select-bordered">
                                    <option selected>1 jour avant</option>
                                    <option>2 jours avant</option>
                                    <option>1 semaine avant</option>
                                </select>
                                </div>
                            </div>
                            </div>
                        </div>
                        
                        <!-- Calendrier -->
                        <div class="card bg-base-200 shadow-md ">
                            <div class="card-body">
                            <h2 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5" />
                                </svg>
                                Synchronisation du calendrier
                            </h2>
                            <p class="text-sm text-base-content/70 mb-4">Synchronisez vos parties et événements avec votre calendrier personnel.</p>
                            
                            <div class="space-y-6">
                                <div>
                                <button class="btn btn-primary gap-2">
                                    <svg class="h-[20px]" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Livello_1" x="0px" y="0px" viewBox="0 0 200 200" enable-background="new 0 0 200 200" xml:space="preserve">
                                        
                                        <g transform="translate(3.75 3.75)">
                                            <path fill="#FFFFFF" d="M148.882,43.618l-47.368-5.263l-57.895,5.263L38.355,96.25l5.263,52.632l52.632,6.579l52.632-6.579    l5.263-53.947L148.882,43.618z"/>
                                            <path fill="#1A73E8" d="M65.211,125.276c-3.934-2.658-6.658-6.539-8.145-11.671l9.132-3.763c0.829,3.158,2.276,5.605,4.342,7.342    c2.053,1.737,4.553,2.592,7.474,2.592c2.987,0,5.553-0.908,7.697-2.724s3.224-4.132,3.224-6.934c0-2.868-1.132-5.211-3.395-7.026    s-5.105-2.724-8.5-2.724h-5.276v-9.039H76.5c2.921,0,5.382-0.789,7.382-2.368c2-1.579,3-3.737,3-6.487    c0-2.447-0.895-4.395-2.684-5.855s-4.053-2.197-6.803-2.197c-2.684,0-4.816,0.711-6.395,2.145s-2.724,3.197-3.447,5.276    l-9.039-3.763c1.197-3.395,3.395-6.395,6.618-8.987c3.224-2.592,7.342-3.895,12.342-3.895c3.697,0,7.026,0.711,9.974,2.145    c2.947,1.434,5.263,3.421,6.934,5.947c1.671,2.539,2.5,5.382,2.5,8.539c0,3.224-0.776,5.947-2.329,8.184    c-1.553,2.237-3.461,3.947-5.724,5.145v0.539c2.987,1.25,5.421,3.158,7.342,5.724c1.908,2.566,2.868,5.632,2.868,9.211    s-0.908,6.776-2.724,9.579c-1.816,2.803-4.329,5.013-7.513,6.618c-3.197,1.605-6.789,2.421-10.776,2.421    C73.408,129.263,69.145,127.934,65.211,125.276z"/>
                                            <path fill="#1A73E8" d="M121.25,79.961l-9.974,7.25l-5.013-7.605l17.987-12.974h6.895v61.197h-9.895L121.25,79.961z"/>
                                            <path fill="#EA4335" d="M148.882,196.25l47.368-47.368l-23.684-10.526l-23.684,10.526l-10.526,23.684L148.882,196.25z"/>
                                            <path fill="#34A853" d="M33.092,172.566l10.526,23.684h105.263v-47.368H43.618L33.092,172.566z"/>
                                            <path fill="#4285F4" d="M12.039-3.75C3.316-3.75-3.75,3.316-3.75,12.039v136.842l23.684,10.526l23.684-10.526V43.618h105.263    l10.526-23.684L148.882-3.75H12.039z"/>
                                            <path fill="#188038" d="M-3.75,148.882v31.579c0,8.724,7.066,15.789,15.789,15.789h31.579v-47.368H-3.75z"/>
                                            <path fill="#FBBC04" d="M148.882,43.618v105.263h47.368V43.618l-23.684-10.526L148.882,43.618z"/>
                                            <path fill="#1967D2" d="M196.25,43.618V12.039c0-8.724-7.066-15.789-15.789-15.789h-31.579v47.368H196.25z"/>
                                        </g>
                                    </svg>
                                    Connecter avec Google Calendar
                                </button>
                                </div>
                                
                                <div>
                                <h3 class="font-medium mb-2">Lien iCalendar (ICS)</h3>
                                <div class="flex gap-2 items-center">
                                    <input type="text" value="https://lbdr-jdr.fr/calendar/ics/user/riley_gaming" class="input input-bordered flex-grow" readonly />
                                    <button class="btn btn-square btn-sm" onclick="copyToClipboard('https://lbdr-jdr.fr/calendar/ics/user/riley_gaming')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-base-content/70 mt-2">
                                    Copiez ce lien et ajoutez-le à votre calendrier préféré (Google Calendar, Apple Calendar, Outlook, etc.) 
                                    pour synchroniser automatiquement vos parties et événements.
                                </p>
                                </div>
                                
                                <div class="divider"></div>
                                
                                <div>
                                <h3 class="font-medium mb-3">Données à synchroniser</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                    <span class="text-sm">Mes parties en tant que MJ</span>
                                    <input type="checkbox" class="toggle toggle-sm toggle-primary" checked />
                                    </div>
                                    <div class="flex items-center justify-between">
                                    <span class="text-sm">Mes parties en tant que joueur</span>
                                    <input type="checkbox" class="toggle toggle-sm toggle-primary" checked />
                                    </div>
                                    <div class="flex items-center justify-between">
                                    <span class="text-sm">Événements de l'association</span>
                                    <input type="checkbox" class="toggle toggle-sm toggle-primary" checked />
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu du tab Paiements -->
                    <div id="tab-content-paiements" class="py-4 hidden">
                      <h3 class="text-lg font-bold mb-4">Mes paiements</h3>
                      
                      <!-- Statut d'adhésion -->
                      <div class="alert alert-success mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Votre adhésion est à jour pour l'année 2025.</span>
                      </div>
                      
                      <!-- Liste des paiements -->
                      <div class="overflow-x-auto">
                        <table class="table table-zebra">
                          <thead>
                            <tr>
                              <th>Date</th>
                              <th>Description</th>
                              <th>Montant</th>
                              <th>Statut</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>15 janvier 2025</td>
                              <td>Adhésion Annuelle 2025</td>
                              <td>25,00 €</td>
                              <td>
                                <div class="badge badge-success">Payé</div>
                              </td>
                              <td>
                                <button class="btn btn-xs btn-outline">Reçu</button>
                              </td>
                            </tr>
                            <tr>
                              <td>10 janvier 2024</td>
                              <td>Adhésion Annuelle 2024</td>
                              <td>25,00 €</td>
                              <td>
                                <div class="badge badge-success">Payé</div>
                              </td>
                              <td>
                                <button class="btn btn-xs btn-outline">Reçu</button>
                              </td>
                            </tr>
                            <tr>
                              <td>5 janvier 2023</td>
                              <td>Adhésion Annuelle 2023</td>
                              <td>20,00 €</td>
                              <td>
                                <div class="badge badge-success">Payé</div>
                              </td>
                              <td>
                                <button class="btn btn-xs btn-outline">Reçu</button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <!-- Bouton pour renouveler l'adhésion -->
                      <div class="mt-6 flex justify-end">
                        <button class="btn btn-success">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                          </svg>
                          Renouveler mon adhésion
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Modal d'édition de profil -->
            <dialog id="edit-profile-modal" class="modal">
              <div class="modal-box w-11/12 max-w-3xl">
                <h3 class="font-bold text-lg">Modifier mon profil</h3>
                <form id="profile-edit-form" class="py-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Prénom</span>
                      </label>
                      <input type="text" id="edit-firstname" class="input input-bordered" value="Thomas" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Nom</span>
                      </label>
                      <input type="text" id="edit-lastname" class="input input-bordered" value="Dubois" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Pseudonyme</span>
                      </label>
                      <input type="text" id="edit-pseudo" class="input input-bordered" value="TomD" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Nom d'utilisateur</span>
                      </label>
                      <input type="text" id="edit-username" class="input input-bordered" value="tomdu92" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Email</span>
                      </label>
                      <input type="email" id="edit-email" class="input input-bordered" value="thomas@email.com" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">ID Discord</span>
                      </label>
                      <input type="text" id="edit-discord" class="input input-bordered" value="TomD#1234" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Date de naissance</span>
                      </label>
                      <input type="date" id="edit-birthdate" class="input input-bordered" value="1990-04-15" />
                    </div>
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Sexe</span>
                      </label>
                      <select id="edit-gender" class="select select-bordered">
                        <option value="M" selected>Masculin</option>
                        <option value="F">Féminin</option>
                        <option value="Autre">Autre</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="divider my-6">Changement de mot de passe</div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                      <label class="label">
                        <span class="label-text">Mot de passe actuel</span>
                      </label>
                      <input type="password" id="edit-current-password" class="input input-bordered" />
                    </div>
                    <div class="form-control md:col-span-2">
                      <label class="label">
                        <span class="label-text">Nouveau mot de passe</span>
                      </label>
                      <input type="password" id="edit-new-password" class="input input-bordered" />
                    </div>
                    <div class="form-control md:col-span-2">
                      <label class="label">
                        <span class="label-text">Confirmer le nouveau mot de passe</span>
                      </label>
                      <input type="password" id="edit-confirm-password" class="input input-bordered" />
                    </div>
                  </div>
                </form>
                <div class="modal-action">
                  <button class="btn btn-error" id="cancel-edit-btn">Annuler</button>
                  <button class="btn btn-primary" id="save-profile-btn">Enregistrer les modifications</button>
                </div>
              </div>
              <form method="dialog" class="modal-backdrop">
                <button>Fermer</button>
              </form>
            </dialog>
            