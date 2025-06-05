{{-- Composant liste de activites par catégorie --}}
@props([
    'titre_section' => 'Liste des activites',
    'sections' => []
])

<div class="my-8">
  <h2 class="text-2xl font-bold mb-2 text-left">{{ $titre_section }}</h2>

  <div class="roll">
    @if(is_array($sections))
      @foreach($sections as $titre => $sessions)
        @include('components.activites.categorie-section', [
          'titre' => $titre,
          'sessions' => $sessions
        ])
      @endforeach
    @endif
  </div>
  
</div>
