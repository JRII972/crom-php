{{-- Composant section de catégorie --}}
@props([
    'titre' => 'Catégorie',
    'parties' => []
])

<div class="roll-section">
  <div class="roll-title gap-4 mb-6">
    <h3 class="text font-semibold md:pl-5">{{ $titre }}</h3>
  </div>

  <!-- Cartes des parties -->
  <div class="roll-content justify-between gap-1 md:gap-2">
    @forelse($parties as $partie)
      @include('components.parties.carte-partie', $partie)
    @empty
      <div class="text-center w-full p-4">
        <p>Aucune partie disponible dans cette catégorie.</p>
      </div>
    @endforelse
  </div>
</div>
