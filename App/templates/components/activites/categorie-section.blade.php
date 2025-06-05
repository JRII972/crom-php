{{-- Composant section de catégorie --}}
@props([
    'titre' => 'Catégorie',
    'sessions' => []
])

<div class="roll-section">
  <div class="roll-title gap-4 mb-6">
    <h3 class="text font-semibold md:pl-5">{{ $titre }}</h3>
  </div>

  <!-- Cartes des activites -->
  <div class="roll-content justify-between gap-1 md:gap-2">
    @if (is_array($sessions))
      @forelse($sessions as $session)
        @include('components.activites.carte-activite', [
          'session' => $session
        ])
      @empty
        <div class="card card-xs md:card-md bg-base-200 rounded-lg shadow-sm overflow-hidden mb-5 h-[280px] w-[185px] min-w-[185px] sm:h-[300px] md:w-[230px] md:h-[350px]">          
          <div class="card-body text-center w-full p-4">
            <p>Aucune activite disponible dans cette catégorie.</p>
          </div>
        </div>
      @endforelse
    @else
      @include('components.activites.carte-activite', [
            'session' => $sessions
          ])
    @endif
  </div>
</div>
