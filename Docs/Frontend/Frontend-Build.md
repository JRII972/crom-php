# Build et Assets Frontend

## Configuration Vite

Le projet utilise **Vite** comme outil de build moderne pour la compilation des assets CSS.

### `vite.config.ts`
Configuration principale de Vite avec :
- Plugin React (pour d'éventuels composants React)
- Plugin Tailwind CSS intégré
- Configuration de build personnalisée

### Processus de Build

```bash
npm run dev        # Mode développement avec watch
npm run build      # Build de production
npm run build:css  # Build CSS + copie vers public/
```

## CSS et Styling

### Stack CSS
- **Tailwind CSS 4.x** : Framework CSS utilitaire
- **DaisyUI 5.x** : Composants UI pré-construits
- **@tailwindcss/typography** : Plugin pour le contenu textuel

### Configuration Tailwind
Le fichier `tailwind.config.js` scanne :
- Templates Blade dans `/App/templates/**/*.blade.php`
- Possibilité d'étendre vers les fichiers PHP et JS

### Workflow CSS
1. **Source** : `/src/main.css` (point d'entrée)
2. **Compilation** : Vite + Tailwind → `/dist/module.css`
3. **Déploiement** : Copie vers `/public/assets/css/module.css`
4. **Utilisation** : Référencé dans les templates Blade

## DaisyUI - Composants

### Avantages
- Composants prêts à l'emploi (boutons, modales, etc.)
- Thèmes multiples intégrés
- Compatible avec Tailwind CSS
- Système de couleurs cohérent

### Exemples d'Utilisation
```html
<!-- Bouton DaisyUI -->
<button class="btn btn-primary">Action</button>

<!-- Carte DaisyUI -->
<div class="card w-96 bg-base-100 shadow-xl">
    <div class="card-body">
        <h2 class="card-title">Titre</h2>
        <p>Contenu de la carte</p>
    </div>
</div>
```

## Scripts NPM

### Scripts Disponibles
```json
{
    "dev": "vite",                    // Serveur de développement
    "build": "vite build",            // Build de production
    "build:css": "...",               // Build + copie CSS
    "watch:css": "...",               // Watch mode avec copie auto
    "lint": "eslint .",               // Linting du code
    "preview": "vite preview"         // Preview du build
}
```

### Watch Mode
Le script `watch:css` utilise `inotifywait` pour :
- Surveiller les changements dans `/dist/module.css`
- Copier automatiquement vers `/public/assets/css/`
- Permettre le développement en temps réel

## Dépendances Frontend

### Dependencies
- `@tailwindcss/cli` & `@tailwindcss/vite` : Outils Tailwind
- `tailwindcss` : Framework CSS principal
- `daisyui` : Composants UI
- `axios` : Client HTTP (si nécessaire)
- `react` & `react-dom` : Framework React (optionnel)

### DevDependencies
- `@vitejs/plugin-react-swc` : Plugin React avec SWC
- `@tailwindcss/typography` : Plugin typographie
- `eslint` : Linting JavaScript/TypeScript
- Types TypeScript pour React

## Intégration avec Blade

### Référencement des Assets
Dans les templates Blade :
```blade
{{-- Layout principal --}}
<link rel="stylesheet" href="/assets/css/module.css">
```

### Utilisation des Classes
```blade
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-primary">
        Titre avec Tailwind + DaisyUI
    </h1>
    
    <button class="btn btn-secondary mt-4">
        Bouton DaisyUI
    </button>
</div>
```

## Optimisation Production

### Vite Build
- Minification automatique
- Tree-shaking des classes CSS inutilisées
- Optimisation des assets
- Génération de hashes pour le cache

### Tailwind Purge
- Suppression automatique des classes non utilisées
- Analyse des fichiers Blade configurés
- Réduction significative de la taille du CSS final
