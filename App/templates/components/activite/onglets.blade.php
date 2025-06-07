{{-- Onglets pour naviguer entre la description et les sessions --}}
<div role="tablist" class="flex justify-evenly lg:justify-start tabs tabs-border">
  <a role="tab" class="tab tab-active {{ $activeTab === 'description' ? 'tab-active' : '' }} md:hidden" id="tab-detail">Détail</a>
  <a role="tab" class="tab {{ $activeTab === 'description' ? 'tab-active' : '' }}" id="tab-description">Description</a>
  <a role="tab" class="tab {{ $activeTab === 'sessions' ? 'tab-active' : '' }}" id="tab-sessions">Sessions 
    @if (count($nextSessions) > 0)
    ({{ count($nextSessions) }})
    @endif
  </a>
  @if ($activite->estMaitreJeu($currentUser))
    <a role="tab" class="tab {{ $activeTab === 'sessions' ? 'tab-active' : '' }}" id="tab-gestion-joueurs">Gestion des joueurs</a>
    <a role="tab" class="tab {{ $activeTab === 'sessions' ? 'tab-active' : '' }}" id="tab-gestion-sessions">Gestion des sessions</a>
  @endif

</div>
