/**
 * Fonctions utilitaires réutilisables
 */

/**
 * Copie un texte dans le presse-papier
 * @param {string} text - Le texte à copier
 * @returns {Promise} Promise qui résout lorsque le texte est copié
 */
function copyToClipboard(text) {
  return navigator.clipboard.writeText(text)
    .then(() => {
      alert('Texte copié !');
    })
    .catch((err) => {
      console.error('Erreur lors de la copie: ', err);
    });
}

/**
 * Initialise les onglets sur une page
 * @param {Object} tabsConfig - Configuration des onglets {tabId: contentId}
 */
function initTabs(tabsConfig) {
  if (!tabsConfig) return;
  
  Object.keys(tabsConfig).forEach(tabId => {
    const tabElement = document.getElementById(tabId);
    if (!tabElement) return;
    
    tabElement.addEventListener('click', function() {
      // Désactiver tous les onglets
      Object.keys(tabsConfig).forEach(id => {
        const tab = document.getElementById(id);
        const content = document.getElementById(tabsConfig[id]);
        if (tab) tab.classList.remove('tab-active');
        if (tab) tab.setAttribute('aria-selected', 'false');
        if (content) content.classList.add('hidden');
      });
      
      // Activer l'onglet cliqué
      this.classList.add('tab-active');
      this.setAttribute('aria-selected', 'true');
      const contentElement = document.getElementById(tabsConfig[tabId]);
      if (contentElement) contentElement.classList.remove('hidden');
    });
  });
}

/**
 * Initialise un menu déroulant pour le filtrage
 * @param {string} btnId - ID du bouton du dropdown
 * @param {string} itemsSelector - Sélecteur pour les éléments du dropdown
 * @param {Function} filterCallback - Fonction de rappel pour le filtrage
 */
function initFilterDropdown(btnId, itemsSelector, filterCallback) {
  const dropdownBtn = document.getElementById(btnId);
  if (!dropdownBtn) return;
  
  const dropdownBtnText = dropdownBtn.querySelector('span');
  const dropdownItems = document.querySelectorAll(itemsSelector);
  let currentFilter = 'all';
  
  // Initialiser les attributs aria-selected
  dropdownItems.forEach(item => {
    if (item.getAttribute('data-value') === 'all') {
      item.classList.add('active', 'selected');
      item.setAttribute('aria-selected', 'true');
    } else {
      item.setAttribute('aria-selected', 'false');
    }
  });
  
  // Ajouter des gestionnaires d'événements aux éléments du menu dropdown
  dropdownItems.forEach(item => {
    item.addEventListener('click', function() {
      // Récupérer la valeur du filtre
      const filterValue = this.getAttribute('data-value');
      const filterText = this.textContent;
      
      // Mettre à jour uniquement le texte du span dans le bouton
      if (dropdownBtnText) {
        dropdownBtnText.textContent = filterText;
      }
      
      // Mettre à jour le filtre actuel
      currentFilter = filterValue;
      
      // Supprimer la classe active et selected de tous les éléments
      // et définir aria-selected="false"
      dropdownItems.forEach(el => {
        el.classList.remove('active', 'selected');
        el.setAttribute('aria-selected', 'false');
      });
      
      // Ajouter la classe active et selected à l'élément cliqué
      // et définir aria-selected="true"
      this.classList.add('active', 'selected');
      this.setAttribute('aria-selected', 'true');
      
      // Filtrer le contenu
      if (typeof filterCallback === 'function') {
        filterCallback(filterValue);
      }
    });
  });
}