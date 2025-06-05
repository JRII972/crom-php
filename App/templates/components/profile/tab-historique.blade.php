{{-- Contenu de l'onglet "Historique" --}}
<div id="tab-content-historique" class="py-4 {{ $activeTab != 'historique' ? 'hidden' : '' }}">
  <h3 class="text-lg font-bold mb-4">Historique des activites</h3>
  
  {{-- Filtres --}}
  <div class="flex flex-wrap gap-2 mb-6">
    <select class="select select-bordered w-full max-w-xs" id="filter-type">
      <option disabled selected>Filtrer par type</option>
      <option value="all">Toutes les activites</option>
      <option value="campagne">Campagnes</option>
      <option value="oneshot">OneShots</option>
      <option value="jeu-societe">Jeux de société</option>
    </select>
    
    <select class="select select-bordered w-full max-w-xs" id="filter-role">
      <option disabled selected>Filtrer par rôle</option>
      <option value="all">Tous les rôles</option>
      <option value="mj">Maître du jeu</option>
      <option value="joueur">Joueur</option>
    </select>
  </div>
  
  {{-- Liste de l'historique --}}
  <div class="overflow-x-auto">
    <table class="table table-zebra">
      <thead>
        <tr>
          <th>Nom de la activite</th>
          <th>Type</th>
          <th>Date</th>
          <th>Rôle</th>
          <th>Lieu</th>
        </tr>
      </thead>
      <tbody>
        @if(isset($historique) && count($historique) > 0)
          @foreach($historique as $activite)
          <tr data-type="{{ $activite->type_slug }}" data-role="{{ $activite->role_slug }}">
            <td>{{ $activite->nom }}</td>
            <td>
              <div class="badge badge-{{ 
                $activite->type == 'Campagne' ? 'primary' : 
                ($activite->type == 'OneShot' ? 'secondary' : 'accent') 
              }}">{{ $activite->type }}</div>
            </td>
            <td>
              @if(isset($activite->date))
                @include('components.date-formatter', ['date' => $activite->date, 'format' => 'j F Y'])
              @else
                -
              @endif
            </td>
            <td>{{ $activite->role }}</td>
            <td>{{ $activite->lieu }}</td>
          </tr>
          @endforeach
        @else
          {{-- Exemples statiques pour démonstration --}}
          <tr data-type="oneshot" data-role="joueur">
            <td>Appel de Cthulhu</td>
            <td>
              <div class="badge badge-secondary">OneShot</div>
            </td>
            <td>15 mai 2025</td>
            <td>Joueur</td>
            <td>Salle Principale</td>
          </tr>
          <tr data-type="campagne" data-role="mj">
            <td>Pathfinder</td>
            <td>
              <div class="badge badge-primary">Campagne</div>
            </td>
            <td>8 mai 2025</td>
            <td>Maître du jeu</td>
            <td>Salle 2</td>
          </tr>
          <tr data-type="jeu-societe" data-role="joueur">
            <td>Dixit</td>
            <td>
              <div class="badge badge-accent">Jeu de société</div>
            </td>
            <td>1 mai 2025</td>
            <td>Joueur</td>
            <td>Salle 3</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>
