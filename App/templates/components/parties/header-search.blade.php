{{-- Composant header avec titre et barre de recherche --}}
<div class="text-center my-6 justify-center align-center">
  <h1 class="text-4xl font-bold font-ravenholm-bold hidden sm:block">Calendrier Rôliste à Option Multiples</h1>
  <h1 class="text-4xl font-bold font-ravenholm-bold md:hidden">CROM</h1>
</div>

<!-- Barre de recherche avec dropdown -->
<div class="flex flex-col sm:flex-row gap-2 mb-6">
  <div class="join sm:flex-1">
    <div id="dropdown-parties" class="dropdown join-item">
      <div tabindex="0" role="button" id="dropdown-parties-btn" class="btn join-item rounded-r-none lg:w-48">
        <span class="hidden lg:block">Toutes les parties</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform group-open:rotate-180">
          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
      <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow-sm">
        <li><a data-value="all" aria-selected="true">Toutes les parties</a></li>
        <li><a data-value="campaigns" aria-selected="false">Campagnes</a></li>
        <li><a data-value="oneshot" aria-selected="false">Oneshot</a></li>
        <li><a data-value="event" aria-selected="false">Événement</a></li>
      </ul>
    </div>

    <div id="search-bar" class="input input-bordered join-item rounded-l-none flex items-center w-full">
      <input type="text" placeholder="Rechercher..." class="grow w-full" />
      <button class="btn btn-ghost btn-sm px-2 flex-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </button>
    </div>
    
    <!-- Groupe de boutons -->
    <div class="join join-item">
      <div>
        <input type="radio" name="view-option" value="list" id="liste-view" class="btn join-item hidden peer" checked aria-label="Vue liste" />
        <label for="liste-view" class="btn join-item flex items-center rounded-none peer-checked:text-primary-content peer-checked:bg-primary hover:border-primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
        </label>
      </div>
      <div>
        <input type="radio" name="view-option" value="grid" id="grid-view" class="btn join-item hidden peer" aria-label="Vue grille" />
        <label for="grid-view" class="btn join-item flex items-center peer-checked:text-primary-content peer-checked:bg-primary hover:border-primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
          </svg>
        </label>
      </div>
    </div>
  </div>
</div>
