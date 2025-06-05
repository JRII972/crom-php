# Guide d'utilisation des Dropdowns

Ce guide explique comment utiliser le système de gestion des dropdowns avec support de sélection simple et multiple.

## Initialisation automatique

Le système s'initialise automatiquement au chargement de la page pour tous les éléments avec la classe `.dropdown`.

## Structure HTML

### Dropdown simple (sélection unique)

```html
<div class="dropdown">
  <div tabindex="0" role="button" class="btn" data-default-text="Choisir une option">
    <span>Choisir une option</span>
    <svg class="w-4 h-4"><!-- icône --></svg>
  </div>
  <ul class="dropdown-content menu">
    <li data-value="option1">Option 1</li>
    <li data-value="option2">Option 2</li>
    <li data-value="option3" class="active">Option 3 (pré-sélectionnée)</li>
  </ul>
</div>
```

### Dropdown multiple (sélection multiple)

```html
<div class="dropdown multiple" id="my-filter">
  <div tabindex="0" role="button" class="btn" data-default-text="Filtres">
    <span>Filtres</span>
    <svg class="w-4 h-4"><!-- icône --></svg>
  </div>
  <ul class="dropdown-content menu">
    <li data-value="filter1">Filtre 1</li>
    <li data-value="filter2" class="active">Filtre 2 (pré-sélectionné)</li>
    <li data-value="filter3">Filtre 3</li>
  </ul>
</div>
```

## Classes CSS importantes

- `.dropdown` : Classe principale pour identifier un dropdown
- `.multiple` : Ajoutée à `.dropdown` pour activer la sélection multiple
- `.active` : Classe ajoutée aux éléments sélectionnés
- `aria-selected="true"` : Attribut ajouté aux éléments sélectionnés

## API JavaScript

### Accès au gestionnaire global

```javascript
const manager = window.dropdownManager;
```

### Ajouter un callback

```javascript
// Pour un dropdown simple
manager.addCallback('my-dropdown-id', (selectedValues, isMultiple) => {
  console.log('Valeur sélectionnée:', selectedValues[0]);
});

// Pour un dropdown multiple
manager.addCallback('my-filter', (selectedValues, isMultiple) => {
  console.log('Valeurs sélectionnées:', selectedValues);
  // Logique de filtrage ici
});
```

### Récupérer les valeurs sélectionnées

```javascript
const selected = manager.getSelectedValues('my-dropdown-id');
console.log('Sélections actuelles:', selected);
```

### Définir les valeurs sélectionnées

```javascript
// Sélection simple
manager.setSelectedValues('my-dropdown-id', ['option2']);

// Sélection multiple
manager.setSelectedValues('my-filter', ['filter1', 'filter3']);
```

### Réinitialiser un dropdown

```javascript
manager.reset('my-dropdown-id');
```

## Feedback visuel

### Classes CSS pour le feedback

Le système ajoute automatiquement ces classes :

- `.btn-active` : Ajoutée au bouton quand des éléments sont sélectionnés
- `.active` : Ajoutée aux éléments sélectionnés dans la liste

### Styles recommandés

```css
/* Bouton actif avec sélection */
.btn.btn-active {
  @apply bg-primary text-primary-content;
}

/* Élément sélectionné dans la liste */
.dropdown-content .active {
  @apply bg-primary text-primary-content;
}

/* Feedback pour sélection multiple */
.dropdown.multiple .btn.btn-active::after {
  content: " (" attr(data-count) ")";
  @apply text-xs opacity-75;
}
```

## Exemples d'utilisation

### Filtre de catégories (simple)

```html
<div class="dropdown" id="category-filter">
  <div tabindex="0" role="button" class="btn" data-default-text="Toutes les catégories">
    <span>Toutes les catégories</span>
  </div>
  <ul class="dropdown-content menu">
    <li data-value="all" class="active">Toutes les catégories</li>
    <li data-value="action">Action</li>
    <li data-value="rpg">RPG</li>
    <li data-value="strategie">Stratégie</li>
  </ul>
</div>

<script>
dropdownManager.addCallback('category-filter', (selectedValues) => {
  const category = selectedValues[0] || 'all';
  filterGamesByCategory(category);
});
</script>
```

### Filtres multiples

```html
<div class="dropdown multiple" id="status-filter">
  <div tabindex="0" role="button" class="btn" data-default-text="Status">
    <span>Status</span>
  </div>
  <ul class="dropdown-content menu">
    <li data-value="ouvert">Ouvert</li>
    <li data-value="en-cours">En cours</li>
    <li data-value="ferme">Fermé</li>
  </ul>
</div>

<script>
dropdownManager.addCallback('status-filter', (selectedValues) => {
  if (selectedValues.length === 0) {
    showAllGames();
  } else {
    filterGamesByStatus(selectedValues);
  }
});
</script>
```

## Migration depuis l'ancien système

L'ancienne fonction `initFilterDropdown` est toujours disponible pour la rétrocompatibilité, mais il est recommandé d'utiliser le nouveau système :

```javascript
// Ancien système (toujours fonctionnel)
initFilterDropdown('filter-btn', '.filter-item', (value) => {
  console.log('Filtre:', value);
});

// Nouveau système (recommandé)
dropdownManager.addCallback('filter-dropdown', (selectedValues, isMultiple) => {
  const value = isMultiple ? selectedValues : selectedValues[0];
  console.log('Filtre:', value);
});
```

## Accessibilité

Le système gère automatiquement :

- Les attributs `aria-selected`
- Le support du clavier (via `tabindex="0"`)
- Les rôles ARIA appropriés

Pour une meilleure accessibilité, ajoutez :

```html
<div class="dropdown" id="my-dropdown" aria-label="Sélectionner une option">
  <div tabindex="0" role="button" class="btn" aria-haspopup="listbox" aria-expanded="false">
    <!-- contenu du bouton -->
  </div>
  <ul class="dropdown-content menu" role="listbox">
    <li role="option" data-value="option1">Option 1</li>
    <!-- autres options -->
  </ul>
</div>
```
