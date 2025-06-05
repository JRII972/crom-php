{{-- Composant pour une session individuelle de activite --}}
<div class="card bg-base-100 shadow-md">
  <div class="card-body p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        @if(is_object($session))
          <h4 class="font-bold">Session #{{ $session->getSessionNumber() }}: {{ $session->getNom() ?? 'Session sans nom' }}</h4>
          <div class="flex flex-wrap gap-2 mt-2">
            <div class="badge badge-outline">{{ $session->getDateSession() ?? 'Date non définie' }}</div>
            <div class="badge badge-outline">{{ $session->getHeureDebut() }} - {{ $session->getHeureFin() }}</div>
            <div class="badge badge-outline">{{ $session->getLieuNom() ?? 'Lieu non défini' }}</div>
            <div class="badge badge-primary badge-outline">{{ count($session->getJoueurs()) }}/{{ $session->getMaxJoueurs() }} places</div>
          </div>
        @endif
      </div>
      <div>
        <button class="btn btn-primary btn-sm">S'inscrire</button>
      </div>
    </div>
    
    {{-- Liste des joueurs inscrits à cette session --}}
    <div class="mt-4">
      <div class="bg-base-200 rounded-lg p-4">
        @if(is_object($session))
          <div class="text-sm font-medium mb-3">
            Joueurs inscrits ({{ count($session->getJoueurs()) }}/{{ $session->getMaxJoueurs() }})
          </div>
          
          @if(count($session->getJoueurs()) > 0)
            <ul class="space-y-2">
              @foreach($session->getJoueurs() as $joueur)
                <li class="flex items-center gap-2">
                  <div class="avatar">
                    <div class="w-6 rounded-full">
                      <img src="{{ $joueur->getImageURL() }}" alt="{{ $joueur->getImageALT() }}" />
                    </div>
                  </div>
                  <span class="text-sm">{{ $joueur->getNom() }} <span class="text-xs opacity-70">{{ $joueur->getPseudonyme() }}</span></span>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-sm italic">Aucun joueur inscrit pour le moment.</p>
          @endif
        @endif
      </div>
    </div>
  </div>
</div>
