<div id="tab-content-gestion-joueurs" class="py-4 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
  <h3 class="text-lg font-bold mb-4">Liste des joueurs</h3>

  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Nom</th>
          <th class="invisible md:visible">Info</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @if(isset($joueurs) && count($joueurs) > 0)
          @foreach($joueurs as $joueur)
            <tr>
              <td>
                <div class="flex items-center gap-3">
                  <div class="avatar">
                    <div class="mask mask-squircle h-12 w-12">
                      <img src="{{ $joueur->getImageURL() }}" alt="{{ $joueur->getImageALT() }}" />
                    </div>
                  </div>
                  <div>
                    <div class="font-bold">{{ $joueur->getNom() }}</div>
                    <div class="text-sm opacity-50">{{ $joueur->displayName() ?? '' }}</div>
                  </div>
                </div>
              </td>
              <td class="invisible md:visible">
                INFO<br />
                <span class="badge badge-ghost badge-sm">Rôle</span>
              </td>
              <td>
                @if($joueur->getIdDiscord())
                  <a href="#" class="btn btn-soft btn-xs btn-info" target="_blank">Discord</a>
                @endif
              </td>
              <td>
                <button class="btn btn-outline btn-xs btn-warning">Désinscrire</button>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="4" class="text-center">Aucun joueur inscrit.</td>
          </tr>
        @endif
      </tbody>
      <tfoot>
        <tr>
          <th>Nom</th>
          <th class="invisible md:visible">Info</th>
          <th></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>