{{-- Description détaillée d'une activite --}}

<div id="tab-content-description" class="py-4">
  <div class="prose prose-lg max-w-none">
    <h3 class="text-2xl font-bold text-primary mb-4">{{ $activite->getNom() }}</h3>
    
    <p class="mb-6 text">
      {{ $activite->getDescription() ?? 'Aucune description disponible pour cette activite.' }}
    </p>
    
  </div>
</div>
