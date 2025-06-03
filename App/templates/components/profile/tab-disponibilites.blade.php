{{-- Contenu de l'onglet "Disponibilités" --}}
<div id="tab-content-disponibilites" class="py-4 {{ $activeTab != 'disponibilites' ? 'hidden' : '' }}">
  <h3 class="text-lg font-bold mb-4">Mes disponibilités</h3>
  
  {{-- Calendrier des disponibilités --}}
  <div class="grid grid-cols-7 gap-2 mb-4">
    <div class="text-center font-medium">Lun</div>
    <div class="text-center font-medium">Mar</div>
    <div class="text-center font-medium">Mer</div>
    <div class="text-center font-medium">Jeu</div>
    <div class="text-center font-medium">Ven</div>
    <div class="text-center font-medium">Sam</div>
    <div class="text-center font-medium">Dim</div>
    
    {{-- Exemple de jours avec disponibilités --}}
    @if(isset($calendar) && count($calendar) > 0)
      @foreach($calendar as $day)
        <div class="btn {{ $day->available ? 'btn-primary' : 'btn-outline' }} btn-sm">{{ $day->number }}</div>
      @endforeach
    @else
      {{-- Exemples statiques pour démonstration --}}
      <div class="btn btn-outline btn-sm">1</div>
      <div class="btn btn-outline btn-sm">2</div>
      <div class="btn btn-primary btn-sm">3</div>
      <div class="btn btn-outline btn-sm">4</div>
      <div class="btn btn-primary btn-sm">5</div>
      <div class="btn btn-primary btn-sm">6</div>
      <div class="btn btn-outline btn-sm">7</div>
      {{-- Continuer pour le reste du mois... --}}
    @endif
  </div>
  
  <div class="divider my-6">Disponibilités récurrentes</div>
  
  {{-- Liste des disponibilités --}}
  <div class="overflow-x-auto">
    <table class="table table-zebra">
      <thead>
        <tr>
          <th>Jour</th>
          <th>Heure de début</th>
          <th>Heure de fin</th>
          <th>Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @if(isset($disponibilites) && count($disponibilites) > 0)
          @foreach($disponibilites as $dispo)
          <tr>
            <td>{{ $dispo->jour }}</td>
            <td>{{ $dispo->heure_debut }}</td>
            <td>{{ $dispo->heure_fin }}</td>
            <td>
              <div class="badge badge-success">{{ $dispo->type }}</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline btn-error" data-id="{{ $dispo->id }}">Supprimer</button>
            </td>
          </tr>
          @endforeach
        @else
          {{-- Exemples statiques pour démonstration --}}
          <tr>
            <td>Mercredi</td>
            <td>19:00</td>
            <td>23:00</td>
            <td>
              <div class="badge badge-success">Disponible</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline btn-error">Supprimer</button>
            </td>
          </tr>
          <tr>
            <td>Vendredi</td>
            <td>20:00</td>
            <td>23:30</td>
            <td>
              <div class="badge badge-success">Disponible</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline btn-error">Supprimer</button>
            </td>
          </tr>
          <tr>
            <td>Samedi</td>
            <td>14:00</td>
            <td>18:00</td>
            <td>
              <div class="badge badge-success">Disponible</div>
            </td>
            <td>
              <button class="btn btn-xs btn-outline btn-error">Supprimer</button>
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
  
  {{-- Bouton ajouter disponibilité --}}
  <div class="mt-6 flex justify-end">
    <button class="btn btn-primary" id="add-availability-btn">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Ajouter une disponibilité
    </button>
  </div>
</div>
