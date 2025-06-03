{{-- Composant pour une session individuelle de partie --}}
<div class="card bg-base-100 shadow-md">
  <div class="card-body p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h4 class="font-bold">Session #{{ $session['numero'] ?? '1' }}: {{ $session['titre'] ?? 'L\'appel du roi' }}</h4>
        <div class="flex flex-wrap gap-2 mt-2">
          <div class="badge badge-outline">{{ $session['date'] ?? '10 Juin 2025' }}</div>
          <div class="badge badge-outline">{{ $session['horaire'] ?? '19h00 - 23h00' }}</div>
          <div class="badge badge-outline">{{ $session['lieu'] ?? 'Salle Paris' }}</div>
          <div class="badge badge-primary badge-outline">{{ $session['places_prises'] ?? '3' }}/{{ $session['places_max'] ?? '5' }} places</div>
        </div>
      </div>
      <div>
        <button class="btn btn-primary btn-sm">S'inscrire</button>
      </div>
    </div>
    
    {{-- Liste des joueurs inscrits à cette session --}}
    <div class="mt-4">
      <div class="bg-base-200 rounded-lg p-4">
        <div class="text-sm font-medium mb-3">
          Joueurs inscrits ({{ $session['places_prises'] ?? '0' }}/{{ $session['places_max'] ?? '5' }})
        </div>
        
        @if(isset($session['joueurs']) && count($session['joueurs']) > 0)
          <ul class="space-y-2">
            @foreach($session['joueurs'] as $joueur)
              <li class="flex items-center gap-2">
                <div class="avatar">
                  <div class="w-6 rounded-full">
                    <img src="{{ $joueur['avatar'] ?? 'https://picsum.photos/seed/player'.($loop->index+1).'/200' }}" alt="Avatar du joueur" />
                  </div>
                </div>
                <span class="text-sm">{{ $joueur['nom'] }} <span class="text-xs opacity-70">{{ $joueur['pseudo'] }}</span></span>
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-sm italic">Aucun joueur inscrit pour le moment.</p>
        @endif
      </div>
    </div>
  </div>
</div>
