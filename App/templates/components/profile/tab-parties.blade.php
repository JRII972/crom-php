{{-- Contenu de l'onglet "Mes Parties" --}}
<div id="tab-content-parties" class="py-4 {{ $activeTab != 'parties' ? 'hidden' : '' }}">
  <h3 class="text-lg font-bold mb-4">Mes parties en cours</h3>
  
  {{-- Liste des parties --}}
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
        @if(isset($parties) && count($parties) > 0)
          @foreach($parties as $partie)
          <tr>
            <td>{{ $partie->nom }}</td>
            <td>
              <div class="badge badge-{{ $partie->type == 'Campagne' ? 'primary' : 'secondary' }}">{{ $partie->type }}</div>
            </td>
            <td>{{ $partie->role }}</td>
            <td>
              @if(isset($partie->prochaine_session))
                @include('components.date-formatter', ['date' => $partie->prochaine_session, 'format' => 'j F Y'])
              @else
                -
              @endif
            </td>
            <td>
              <a href="{{ $route('parties.show', [$partie->id]) }}" class="btn btn-xs btn-outline">Voir</a>
            </td>
          </tr>
          @endforeach
        @else
          {{-- Exemples statiques pour démonstration --}}
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
        @endif
      </tbody>
    </table>
  </div>
  
  {{-- Bouton créer une partie --}}
  <div class="mt-6 flex justify-end">
    <a href="{{ $route('parties.create') }}" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Créer une nouvelle partie
    </a>
  </div>
</div>
