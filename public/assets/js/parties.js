/**
 * Scripts spécifiques à la page des parties
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialiser le dropdown de filtrage des parties
  initFilterDropdown(
    'dropdown-parties-btn', 
    '#dropdown-parties .dropdown-content a', 
    filterParties
  );

  // Initialiser les vues (liste/grille)
  initViewToggle();
});

/**
 * Filtre les parties selon le critère sélectionné
 * @param {string} filter - Le filtre à appliquer
 */
function filterParties(filter) {
  console.log(`Filtrage des parties par: ${filter}`);
  // Logique de filtrage à implémenter
  // Exemple: 
  // const allItems = document.querySelectorAll('.partie-item');
  // allItems.forEach(item => {
  //   if (filter === 'all' || item.classList.contains(`partie-${filter}`)) {
  //     item.classList.remove('hidden');
  //   } else {
  //     item.classList.add('hidden');
  //   }
  // });
}

/**
 * Initialise la bascule entre vue liste et vue grille
 */
function initViewToggle() {
  const listView = document.getElementById('liste-view');
  const gridView = document.getElementById('grid-view');
  
  if (!listView || !gridView) return;
  
  listView.addEventListener('change', function() {
    if (this.checked) {
      // Logique pour afficher en mode liste
      console.log('Vue liste activée');
    }
  });
  
  gridView.addEventListener('change', function() {
    if (this.checked) {
      // Logique pour afficher en mode grille
      console.log('Vue grille activée');
    }
  });
}