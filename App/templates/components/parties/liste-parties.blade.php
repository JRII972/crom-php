{{-- Composant liste de parties par catégorie --}}
@props([
    'titre_section' => 'Liste des parties',
    'categories' => [],
    'voir_plus' => true
])

<div class="my-8">
  <h2 class="text-2xl font-bold mb-2 text-left">{{ $titre_section }}</h2>

  <div class="roll">
    @foreach($categories as $categorie)
      @include('components.parties.categorie-section', $categorie)
    @endforeach
  </div>
  
  @if($voir_plus)
    <!-- Bouton "Voir plus" -->
    <div class="flex justify-center mt-6">
      <button class="btn btn-outline">Voir plus de parties</button>
    </div>
  @endif
</div>
