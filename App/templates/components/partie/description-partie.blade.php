{{-- Description détaillée d'une partie --}}
<div id="tab-content-description" class="py-4">
  <div class="prose prose-lg max-w-none">
    <h3 class="text-2xl font-bold text-primary mb-4">{{ $partie['titre_description'] ?? 'Bienvenue dans Les Légendes d\'Andor' }}</h3>
    
    @if(isset($partie['description']) && is_array($partie['description']))
      @foreach($partie['description'] as $paragraphe)
        <p class="mb-4">{{ $paragraphe }}</p>
      @endforeach
    @else
      <p class="mb-4">
        Le royaume d'Andor est menacé. Des hordes de créatures maléfiques déferlent sur le pays, attirées par les richesses et le pouvoir. Le roi est en difficulté et appelle à l'aide tous les héros disponibles.
      </p>
      <p class="mb-6">
        Cette campagne vous plongera dans un monde médiéval-fantastique riche en aventures, en mystères et en dangers. Vous incarnerez des héros aux capacités diverses, devant collaborer pour sauver le royaume des forces obscures qui le menacent.
      </p>
    @endif
    
    <h4 class="text-xl font-semibold text-secondary mb-3">Ce que vous devez savoir</h4>
    <ul class="list-disc pl-6 mb-6 space-y-2">
      <li>Niveau de départ des personnages: <span class="font-medium">{{ $partie['niveau_depart'] ?? '3' }}</span></li>
      <li>Règles: <span class="font-medium">{{ $partie['regles'] ?? 'D&D 5e' }}</span> avec quelques modifications mineures</li>
      <li>Style de jeu: {{ $partie['style_jeu'] ?? 'Équilibre entre combat, exploration et interaction sociale' }}</li>
      <li>Ton: {{ $partie['ton'] ?? 'Heroic fantasy, avec des moments plus sombres' }}</li>
      <li>Thèmes sensibles: {{ $partie['themes'] ?? 'Violence modérée, quelques éléments d\'horreur' }}</li>
    </ul>
    
    @if(isset($partie['citation']))
      <blockquote class="bg-base-300 p-4 rounded-lg border-l-4 border-primary italic mb-6">
        "{{ $partie['citation']['texte'] }}" - <span class="font-medium">{{ $partie['citation']['auteur'] }}</span>
      </blockquote>
    @else
      <blockquote class="bg-base-300 p-4 rounded-lg border-l-4 border-primary italic mb-6">
        "Un grand pouvoir implique de grandes responsabilités. Vos choix façonneront l'avenir d'Andor." - <span class="font-medium">Le Roi Brandur</span>
      </blockquote>
    @endif
    
    <h4 class="text-xl font-semibold text-secondary mb-3">Livres autorisés</h4>
    <p class="mb-4">
      {{ $partie['livres_autorises'] ?? 'Manuel des Joueurs, Guide du Maître, Manuel des Monstres, Guide de Xanathar.' }}
    </p>
  </div>
</div>
