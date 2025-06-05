<!-- Filtres additionnels -->
<div class="flex justify-between">
  <!-- Bouton pour ouvrir le drawer des filtres (mobile uniquement) -->
  <div class="lg:hidden">
    <label for="filters-drawer" class="btn btn-outline btn-sm">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
      </svg>
      Filtres
    </label>
  </div>

  <!-- Filtres desktop (cachés sur mobile) -->
  <div class="hidden lg:flex flex-wrap gap-4 mb-6">
    <div class="dropdown multiple relative group">
      <div tabindex="0" role="button" class="flex items-center gap-2 text-sm cursor-pointer select-none">
        <span>Par Jour</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform group-open:rotate-180">
          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-md">
        <li><a>Vendredi</a></li>
        <li><a>Samedi</a></li>
        <li><a>Dimanche</a></li>
      </ul>
    </div>

    <div class="dropdown multiple relative group">
      <div tabindex="0" role="button" class="flex items-center gap-2 text-sm cursor-pointer select-none">
        <span>Par lieu</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform group-open:rotate-180">
          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-md">
        <li><a>Foyer Saint Vincent</a></li>
        <li><a>Maison des Association centre</a></li>
        <li><a>Maison des association sud</a></li>
      </ul>
    </div>

    <div class="dropdown multiple relative group" data-title="Type de campagne">
      <div tabindex="0" role="button" class="flex items-center gap-2 text-sm cursor-pointer select-none">
        <span>Par type de campagne</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform group-open:rotate-180">
          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-md">
        <li><a>Campagne fermée</a></li>
        <li><a>Campagne ouverte</a></li>
      </ul>
    </div>

    <div class="dropdown multiple relative group">
      <div tabindex="0" role="button" class="flex items-center gap-2 text-sm cursor-pointer select-none">
        <span>Par style</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform group-open:rotate-180">
          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-md">
        <li><a>Fantaisie</a></li>
        <li><a>Médiéval</a></li>
        <li><a>Espace</a></li>
        <li><a>Combat</a></li>
      </ul>
    </div>
  </div>

  <!-- Bouton de tri (visible sur toutes les tailles d'écran) -->
  <div class="flex flex-wrap gap-4 mb-6">
    <div class="dropdown relative group">
      <div tabindex="0" role="button" class="flex items-center gap-2 text-sm cursor-pointer select-none">
        <span>Trie</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform group-open:rotate-180">
          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-md">
        <li><a>Par date</a></li>
        <li><a>Par type</a></li>
        <li><a>Par genre</a></li>
      </ul>
    </div>
  </div>
</div>

<!-- Drawer pour les filtres (mobile uniquement) -->
<div class="drawer drawer-end lg:hidden">
  <input id="filters-drawer" type="checkbox" class="drawer-toggle" />
  <div class="drawer-side z-50">
    <label for="filters-drawer" class="drawer-overlay"></label>
    <div class="w-80 min-h-full bg-base-100 p-4">
      <!-- En-tête du drawer -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold">Filtres</h3>
        <label for="filters-drawer" class="btn btn-ghost btn-sm btn-circle">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </label>
      </div>

      <!-- Contenu des filtres -->
      <div class="space-y-6">
        <!-- Filtre par jour -->
        <section>
          <h4 class="text-md font-medium mb-3">Par Jour</h4>
          <div class="space-y-2">
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Vendredi</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Samedi</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Dimanche</span>
            </label>
          </div>
        </section>

        <div class="divider my-4"></div>

        <!-- Filtre par lieu -->
        <section>
          <h4 class="text-md font-medium mb-3">Par lieu</h4>
          <div class="space-y-2">
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Foyer Saint Vincent</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Maison des Association centre</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Maison des association sud</span>
            </label>
          </div>
        </section>

        <div class="divider my-4"></div>

        <!-- Filtre par type de campagne -->
        <section>
          <h4 class="text-md font-medium mb-3">Par type de campagne</h4>
          <div class="space-y-2">
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Campagne fermée</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Campagne ouverte</span>
            </label>
          </div>
        </section>

        <div class="divider my-4"></div>

        <!-- Filtre par style -->
        <section>
          <h4 class="text-md font-medium mb-3">Par style</h4>
          <div class="space-y-2">
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Fantaisie</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Médiéval</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Espace</span>
            </label>
            <label class="label cursor-pointer justify-start gap-3">
              <input type="checkbox" class="checkbox checkbox-sm" />
              <span class="label-text">Combat</span>
            </label>
          </div>
        </section>
      </div>

      <!-- Boutons d'action -->
      <div class="flex gap-2 mt-6">
        <button class="btn btn-outline btn-sm flex-1">Réinitialiser</button>
        <label for="filters-drawer" class="btn btn-primary btn-sm flex-1">Appliquer</label>
      </div>
    </div>
  </div>
</div>