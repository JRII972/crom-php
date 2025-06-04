{{-- Composant section de catégorie --}}
@props([
    'titre' => 'Catégorie',
    'sessions' => []
])

<div class="roll-section">
  <div class="roll-title gap-4 mb-6">
    <h3 class="text font-semibold md:pl-5">{{ $titre }}</h3>
  </div>

  <!-- Cartes des parties -->
  <div class="roll-content justify-between gap-1 md:gap-2">
    @forelse($sessions as $session)
      @include('components.parties.carte-partie', [
        'session' => $session
      ])
    @empty
      <div class="text-center w-full p-4">
        <p>Aucune partie disponible dans cette catégorie.</p>
      </div>
    @endforelse
  </div>
</div>
