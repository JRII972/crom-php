{{-- Liste des joueurs inscrits à une partie --}}
<div class="card bg-base-200 shadow-xl mt-6" id="partie-joueurs-container">
  <div class="card-body">
    <h3 class="card-title text-lg">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
      </svg>
      Joueurs inscrits
    </h3>
    <div class="divider my-1"></div>
    <ul class="space-y-3" id="liste-joueurs">
      @if(isset($joueurs) && count($joueurs) > 0)
        @foreach($joueurs as $joueur)
          <li class="flex items-center gap-3">
            <div class="avatar">
              <div class="w-10 rounded-full">
                <img src="{{ $joueur['avatar'] ?? 'https://picsum.photos/200' }}" alt="Avatar joueur" />
              </div>
            </div>
            <div>
              <div class="font-medium">{{ $joueur['nom'] }}</div>
              <div class="text-xs text-base-content/70">{{ $joueur['pseudo'] }}</div>
            </div>
          </li>
        @endforeach
      @else
        {{-- Exemple de joueurs statiques si aucun joueur n'est fourni --}}
        <li class="flex items-center gap-3">
          <div class="avatar">
            <div class="w-10 rounded-full">
              <img src="https://picsum.photos/200" alt="Avatar joueur" />
            </div>
          </div>
          <div>
            <div class="font-medium">Emma Martin</div>
            <div class="text-xs text-base-content/70">@Emm4</div>
          </div>
        </li>
        <li class="flex items-center gap-3">
          <div class="avatar">
            <div class="w-10 rounded-full">
              <img src="https://picsum.photos/201" alt="Avatar joueur" />
            </div>
          </div>
          <div>
            <div class="font-medium">Lucas Bernard</div>
            <div class="text-xs text-base-content/70">@LucasB</div>
          </div>
        </li>
        <li class="flex items-center gap-3">
          <div class="avatar">
            <div class="w-10 rounded-full">
              <img src="https://picsum.photos/202" alt="Avatar joueur" />
            </div>
          </div>
          <div>
            <div class="font-medium">Chloé Petit</div>
            <div class="text-xs text-base-content/70">@ChloeP</div>
          </div>
        </li>
      @endif
    </ul>
  </div>
</div>
