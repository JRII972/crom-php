{{-- Informations de base d'une partie (image, titre, type, places disponibles, etc.) --}}
<div class="card bg-base-200 shadow-xl overflow-hidden">
  {{-- Image de la partie --}}
  <figure class="relative">
    <img src="{{ $partie['image'] ?? 'https://picsum.photos/seed/andor/800/400' }}" alt="{{ $partie['nom'] ?? 'Les Légendes d\'Andor' }}" class="w-full h-60 object-cover" />
    <div class="absolute top-3 right-3">
      <div class="badge badge-lg" id="partie-type-badge">{{ $partie['type'] ?? 'Campagne' }}</div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 text-white">
      <h2 class="card-title text-2xl mb-1" id="partie-nom">{{ $partie['nom'] ?? 'Les Légendes d\'Andor' }}</h2>
      <p class="badge badge-sm badge-secondary opacity-80" id="partie-jeu">Jeu: {{ $partie['jeu'] ?? 'Donjons & Dragons 5e' }}</p>
    </div>
  </figure>
  
  <div class="card-body">
    {{-- Type de campagne si applicable --}}
    <div class="flex gap-2 mb-4" id="campagne-info">
      <div class="badge badge-outline badge-accent" id="type-campagne-badge">{{ $partie['type_campagne'] ?? 'Campagne Ouverte' }}</div>
      <div class="badge badge-outline" id="partie-place-badge">{{ $partie['places_prises'] ?? '3' }}/{{ $partie['places_max'] ?? '5' }} places</div>
    </div>
    
    {{-- Maître du jeu --}}
    <div class="flex items-center gap-3 mb-4">
      <div class="avatar">
        <div class="w-10 rounded-full">
          <img src="{{ $partie['mj_avatar'] ?? 'https://picsum.photos/seed/dm/200' }}" alt="Avatar du MJ" />
        </div>
      </div>
      <div>
        <p class="text-sm opacity-70">Maître du jeu</p>
        <p class="font-medium" id="partie-mj">{{ $partie['mj_nom'] ?? 'Alex Martin' }} <span class="text-sm opacity-70">{{ $partie['mj_pseudo'] ?? '@DungeonMaster' }}</span></p>
      </div>
    </div>
    
    {{-- Informations de session --}}
    <div class="flex flex-col gap-2">
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5" />
        </svg>
        <span id="partie-date-creation">Créée le {{ $partie['date_creation'] ?? '15 Mai 2025' }}</span>
      </div>
      
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
        <span id="partie-joueurs-max">{{ $partie['places_max'] ?? '5' }} joueurs maximum</span>
      </div>
      
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
        </svg>
        <span id="partie-lieu">{{ $partie['lieu'] ?? 'Salle Paris - Local LBDR' }}</span>
      </div>
    </div>
    
    {{-- Bouton d'inscription pour les campagnes --}}
    <div class="card-actions justify-end mt-6" id="inscription-partie-container">
      <button class="btn btn-primary w-full" id="inscription-partie-btn">S'inscrire à cette campagne</button>
    </div>
    
    {{-- Notification d'inscription requise pour campagnes fermées --}}
    <div class="alert alert-warning mt-4 hidden" id="inscription-required-alert">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
      <span>L'inscription à cette campagne est requise pour participer aux sessions.</span>
    </div>
    
    {{-- Notification de campagne complète --}}
    <div class="alert alert-error mt-4 hidden" id="campagne-complete-alert">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      <span>Cette campagne est complète. Vous ne pouvez plus vous y inscrire.</span>
    </div>
  </div>
</div>
