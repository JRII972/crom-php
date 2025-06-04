{{-- Description détaillée d'une partie --}}
@props([
    'partie'
  ])

<div id="tab-content-description" class="py-4">
  <div class="prose prose-lg max-w-none">
    <h3 class="text-2xl font-bold text-primary mb-4">{{ $partie->getNom() }}</h3>
    
    <p class="mb-6">
      {{ $partie->getDescription() ?? 'Aucune description disponible pour cette partie.' }}
    </p>
    
    <h4 class="text-xl font-semibold text-secondary mb-3">Informations de la partie</h4>
    <ul class="list-disc pl-6 mb-6 space-y-2">
      <li>Type: <span class="font-medium">{{ $partie->getTypeFormatted() }}</span></li>
      <li>Jeu: <span class="font-medium">{{ $partie->getNomJeu() }}</span></li>
      <li>Maître du jeu: <span class="font-medium">{{ $partie->getMaitreJeu()->getNom() }}</span></li>
      <li>Nombre de joueurs: <span class="font-medium">{{ $partie->getMaxJoueurs() }} maximum</span></li>
      <li>Statut: <span class="font-medium">{{ $partie->getStatutFormatted() }}</span></li>
    </ul>
    
    <!-- @if(isset($partie['citation']))
      <blockquote class="bg-base-300 p-4 rounded-lg border-l-4 border-primary italic mb-6">
        "{{ $partie['citation']['texte'] }}" - <span class="font-medium">{{ $partie['citation']['auteur'] }}</span>
      </blockquote>
    @else
      <blockquote class="bg-base-300 p-4 rounded-lg border-l-4 border-primary italic mb-6">
        "Un grand pouvoir implique de grandes responsabilités. Vos choix façonneront l'avenir d'Andor." - <span class="font-medium">Le Roi Brandur</span>
      </blockquote>
    @endif -->
    
  </div>
</div>
