<div id="tab-content-gestion-sessions" class="tab-content">
  <h3 class="text-lg font-bold mb-4">Liste des sessions</h3>

  <div class="overflow-x-auto">
    <div class="overflow-x-auto">
      <table class="table table-xs">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Titre</th>
            <th>Etat</th>
            <th>Lieu</th>
            <th>Nombre inscrit</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @if(isset($sessions) && count($sessions) > 0)
            @foreach($sessions as $i => $session)
              <tr>
                <th>{{ $i+1 }}</th>
                <td>{{ \Carbon\Carbon::parse($session->getDateSession())->format('d/m/Y') }}</td>
                <td>{{ $session->getNom() }}</td>
                <td>
                  @php
                    $etat = $session->getEtat()->value;
                    $badgeClass = match($etat) {
                      'OUVERTE' => 'badge-info',
                      'FERMER' => 'badge-warning',
                      'ANNULER' => 'badge-error',
                      'COMPLETE' => 'badge-success',
                      default => 'badge-neutral',
                    };
                  @endphp
                  <div class="badge badge-sm badge-soft {{ $badgeClass }}">
                    {{ ucfirst(strtolower($etat)) }}
                  </div>
                </td>
                <td>{{ $session->getLieuNom() }}</td>
                <td>{{ count($session->getJoueurs()) }}/{{ $session->getMaxJoueurs() }}</td>
                <td>
                  @if($session->getEtat()->value === 'OUVERTE' || $session->getEtat()->value === 'COMPLETE')
                    <button class="btn btn-soft btn-warning btn-xs">Annuler</button>
                  @endif
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="7" class="text-center">Aucune session trouvée.</td>
            </tr>
          @endif
        </tbody>
        <tfoot>
          <tr>
            <th></th>
            <th>Date</th>
            <th>Titre</th>
            <th>Etat</th>
            <th>Lieu</th>
            <th>Nombre inscrit</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>