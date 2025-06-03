{{-- Navigation par onglets du profil utilisateur --}}
<div role="tablist" class="tabs tabs-lifted">
  <a role="tab" class="tab {{ $activeTab == 'parties' ? 'tab-active' : '' }}" id="tab-parties" aria-selected="{{ $activeTab == 'parties' ? 'true' : 'false' }}">Mes Parties</a>
  <a role="tab" class="tab {{ $activeTab == 'disponibilites' ? 'tab-active' : '' }}" id="tab-disponibilites" aria-selected="{{ $activeTab == 'disponibilites' ? 'true' : 'false' }}">Disponibilités</a>
  <a role="tab" class="tab {{ $activeTab == 'historique' ? 'tab-active' : '' }}" id="tab-historique" aria-selected="{{ $activeTab == 'historique' ? 'true' : 'false' }}">Historique</a>
  <a role="tab" class="tab {{ $activeTab == 'preference' ? 'tab-active' : '' }}" id="tab-preference" aria-selected="{{ $activeTab == 'preference' ? 'true' : 'false' }}">Préférences</a>
  <a role="tab" class="tab {{ $activeTab == 'paiements' ? 'tab-active' : '' }}" id="tab-paiements" aria-selected="{{ $activeTab == 'paiements' ? 'true' : 'false' }}">Paiements</a>
</div>
