<div id="tab-content-gestion-sessions" class="py-4 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
  <h3 class="text-lg font-bold mb-4">List des sessions</h3>

  <div class="overflow-x-auto">
    <div class="overflow-x-auto">
      <table class="table table-xs">
        <thead>
          <tr>
            <th></th>
            <th>Date</th>
            <th>Titre</th>
            <th>Etat</th>
            <th>Lieu</th>
            <th>Nombre inscrit</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>1</th>
            <td>12/06/2025</td>
            <td>Quality Control Specialist</td>
            <td>
              <div class="badge badge-sm badge-soft  badge-info">Ouverte</div>
            </td>
            <td>Foyer Saint Vincent</td>
            <td>4/5</td>
            <td>
              <button class="btn btn-soft btn-warning btn-xs"> Annuler</button>
            </td>
          </tr>
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