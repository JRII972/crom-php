{{-- Liste des sessions d'une partie --}}

<div id="tab-content-sessions" class="py-4 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
  <h3 class="text-lg font-bold mb-4">Prochaines sessions</h3>
  
  {{-- Liste des sessions --}}
  <div class="space-y-6">
    @if(isset($sessions) && count($sessions) > 0)
      @foreach($sessions as $session)
        @if ($session->getDateSession() >= date('Y-m-d'))
          @include('components.partie.session-item', ['session' => $session])        
        @endif
      @endforeach
    @endif
  </div>
</div>
