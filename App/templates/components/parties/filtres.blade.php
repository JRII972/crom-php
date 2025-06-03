{{-- Composant filtres additionnels --}}
<div class="flex flex-wrap gap-4 mb-6">
  <div class="dropdown relative group">
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

  <div class="dropdown relative group">
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

  <div class="dropdown relative group">
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

  <div class="dropdown relative group">
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
