{{-- Description détaillée d'une partie --}}

<div id="tab-content-description" class="py-4">
  <div class="prose prose-lg max-w-none">
    <h3 class="text-2xl font-bold text-primary mb-4">{{ $partie->getNom() }}</h3>
    
    <p class="mb-6 text">
      {{ $partie->getDescription() ?? 'Aucune description disponible pour cette partie.' }}
    </p>
    
  </div>
</div>
