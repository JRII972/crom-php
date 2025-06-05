{{-- Contenu de l'onglet "Préférences" --}}
<div id="tab-content-preference" class="py-4 {{ $activeTab != 'preference' ? 'hidden' : '' }}">
  {{-- Préférences d'affichage des cartes --}}
  <div class="card bg-base-200 shadow-md mb-6 text-left">
    <div class="card-body items-start">
      <h2 class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
        </svg>
        Préférences d'affichage des cartes
      </h2>
      <p class="text-sm text-base-content/70 mb-4">Personnalisez l'affichage des cartes de activites et d'événements.</p>
      
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium">Afficher les joueurs</h3>
            <p class="text-sm text-base-content/70">Montrer la liste des joueurs inscrits sur les cartes de activites</p>
          </div>
          <input type="checkbox" class="toggle toggle-primary" {{ isset($preferences->show_players) && $preferences->show_players ? 'checked' : '' }} data-pref="show_players" />
        </div>
        
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium">Afficher les noms de activites</h3>
            <p class="text-sm text-base-content/70">Montrer les noms complets des activites plutôt que des descriptions courtes</p>
          </div>
          <input type="checkbox" class="toggle toggle-primary" {{ isset($preferences->show_full_names) && $preferences->show_full_names ? 'checked' : '' }} data-pref="show_full_names" />
        </div>
        
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium">Afficher les pseudonymes</h3>
            <p class="text-sm text-base-content/70">Utiliser les pseudonymes au lieu des noms réels des utilisateurs</p>
          </div>
          <input type="checkbox" class="toggle toggle-primary" {{ isset($preferences->show_pseudos) && $preferences->show_pseudos ? 'checked' : '' }} data-pref="show_pseudos" />
        </div>
      </div>
    </div>
  </div>
  
  {{-- Notifications --}}
  <div class="card bg-base-200 shadow-md mb-6 text-left">
    <div class="card-body">
      <h2 class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        Notifications et rappels
      </h2>
      <p class="text-sm text-base-content/70 mb-4">Gérez vos préférences de notifications et rappels pour les activites et événements.</p>
      
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium">Afficher des informations personnalisées</h3>
            <p class="text-sm text-base-content/70">Recevoir des recommandations basées sur vos préférences et votre historique</p>
          </div>
          <input type="checkbox" class="toggle toggle-primary" {{ isset($preferences->custom_info) && $preferences->custom_info ? 'checked' : '' }} data-pref="custom_info" />
        </div>
        
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-medium">Envoyer des notifications de rappel</h3>
            <p class="text-sm text-base-content/70">Recevoir des rappels pour vos activites à venir</p>
          </div>
          <input type="checkbox" class="toggle toggle-primary" {{ isset($preferences->reminders) && $preferences->reminders ? 'checked' : '' }} data-pref="reminders" />
        </div>
        
        <div class="form-control w-full max-w-xs ml-auto">
          <label class="label">
            <span class="label-text">Délai de rappel</span>
          </label>
          <select class="select select-bordered" id="reminder-delay">
            <option {{ isset($preferences->reminder_delay) && $preferences->reminder_delay == '1' ? 'selected' : '' }} value="1">1 jour avant</option>
            <option {{ isset($preferences->reminder_delay) && $preferences->reminder_delay == '2' ? 'selected' : '' }} value="2">2 jours avant</option>
            <option {{ isset($preferences->reminder_delay) && $preferences->reminder_delay == '7' ? 'selected' : '' }} value="7">1 semaine avant</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  
  {{-- Calendrier --}}
  <div class="card bg-base-200 shadow-md">
    <div class="card-body">
      <h2 class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9.75v7.5" />
        </svg>
        Synchronisation du calendrier
      </h2>
      <p class="text-sm text-base-content/70 mb-4">Synchronisez vos activites et événements avec votre calendrier personnel.</p>
      
      <div class="space-y-6">
        <div>
          <button class="btn btn-primary gap-2" id="sync-gcal">
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
            <input type="text" value="{{ $calendarUrl ?? 'https://lbdr-jdr.fr/calendar/ics/user/riley_gaming' }}" class="input input-bordered flex-grow" id="ics-url" readonly />
            <button class="btn btn-square btn-sm" id="copy-ics-url">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </button>
          </div>
          <p class="text-xs text-base-content/70 mt-2">
            Copiez ce lien et ajoutez-le à votre calendrier préféré (Google Calendar, Apple Calendar, Outlook, etc.) 
            pour synchroniser automatiquement vos activites et événements.
          </p>
        </div>
        
        <div class="divider"></div>
        
        <div>
          <h3 class="font-medium mb-3">Données à synchroniser</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm">Mes activites en tant que MJ</span>
              <input type="checkbox" class="toggle toggle-sm toggle-primary" {{ isset($preferences->sync_mj) && $preferences->sync_mj ? 'checked' : '' }} data-pref="sync_mj" />
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm">Mes activites en tant que joueur</span>
              <input type="checkbox" class="toggle toggle-sm toggle-primary" {{ isset($preferences->sync_player) && $preferences->sync_player ? 'checked' : '' }} data-pref="sync_player" />
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm">Événements de l'association</span>
              <input type="checkbox" class="toggle toggle-sm toggle-primary" {{ isset($preferences->sync_events) && $preferences->sync_events ? 'checked' : '' }} data-pref="sync_events" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
