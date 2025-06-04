{{-- Composant liste de parties par catégorie --}}
@props([
    'titre_section' => 'Liste des parties',
    'sections' => []
])

<div class="my-8">
  <h2 class="text-2xl font-bold mb-2 text-left">{{ $titre_section }}</h2>

  <div class="roll">
    @foreach($sections as $titre => $sessions)
      @include('components.parties.categorie-section', [
        'titre' => $titre,
        'sessions' => $sessions
      ])
    @endforeach
  </div>
  
</div>
