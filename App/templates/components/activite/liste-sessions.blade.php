{{-- Liste des sessions d'une activite --}}

<div id="tab-content-sessions" class="py-4 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
  <h3 class="text-lg font-bold mb-4">Prochaines sessions</h3>
  
  {{-- Liste des sessions --}}
  <div class="space-y-6">
    @forelse($nextSessions as $session)
      @if ($session->getDateSession() >= date('Y-m-d'))
        @include('components.activite.session-item', ['session' => $session])        
      @endif
    @empty
    <div>
      <h4 class="text"> Pas de prochaines sessions programmé :(</h4>
    </div>
    @endforelse
  </div>
</div>
