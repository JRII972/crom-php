<div id="tab-content-gestion-joueurs" class="py-4 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
  <h3 class="text-lg font-bold mb-4">List des joueur</h3>

  <div class="overflow-x-auto">
    <table class="table">
      <!-- head -->
      <thead>
        <tr>
          <th>Nom</th>
          <th class="invisible md:visible">Info</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <!-- row 1 -->
        <tr>
          <td>
            <div class="flex items-center gap-3">
              <div class="avatar">
                <div class="mask mask-squircle h-12 w-12">
                  <img src="https://img.daisyui.com/images/profile/demo/2@94.webp"
                    alt="Avatar Tailwind CSS Component" />
                </div>
              </div>
              <div>
                <div class="font-bold">Hart Hagerty</div>
                <div class="text-sm opacity-50">United States</div>
              </div>
            </div>
          </td>
          <td class="invisible md:visible">
            Zemlak, Daniel and Leannon
            <br />
            <span class="badge badge-ghost badge-sm">Desktop Support Technician</span>
          </td>
          <td>
            <button class="btn btn-soft btn-xs btn-info">Discord</button>
          </td>
          <td>
            <button class="btn btn-outline btn-xs btn-warning">Désinscrire</button>
          </td>
        </tr>
      </tbody>
      <!-- foot -->
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