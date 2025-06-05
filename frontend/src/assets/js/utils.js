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
 * Gestionnaire général des dropdowns avec support de sélection simple et multiple
 */
class DropdownManager {
  constructor() {
    this.dropdowns = new Map();
    this.init();
  }

  /**
   * Initialise automatiquement tous les dropdowns de la page
   */
  init() {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
      this.initDropdown(dropdown);
    });
  }

  /**
   * Initialise un dropdown spécifique
   * @param {HTMLElement} dropdown - Élément dropdown
   */
  initDropdown(dropdown) {
    const dropdownId = dropdown.id || `dropdown-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    if (!dropdown.id) dropdown.id = dropdownId;

    const isMultiple = dropdown.classList.contains('multiple');
    const button = dropdown.querySelector('[role="button"], summary, button');
    const content = dropdown.querySelector('.dropdown-content');
    const items = content ? content.querySelectorAll('li, [role="option"], .dropdown-item') : [];

    if (!button || !content || items.length === 0) {
      console.warn('Dropdown invalide:', dropdown);
      return;
    }

    const config = {
      id: dropdownId,
      element: dropdown,
      button,
      content,
      items: Array.from(items),
      isMultiple,
      selectedItems: new Set(),
      callbacks: new Set()
    };

    this.dropdowns.set(dropdownId, config);
    this.bindEvents(config);
    this.initializeItems(config);
  }

  /**
   * Initialise les éléments du dropdown
   * @param {Object} config - Configuration du dropdown
   */
  initializeItems(config) {
    config.items.forEach(item => {
      const isSelected = item.classList.contains('active') || 
                        item.hasAttribute('selected') ||
                        item.getAttribute('aria-selected') === 'true';
      
      if (isSelected) {
        this.selectItem(config, item, false);
      } else {
        item.setAttribute('aria-selected', 'false');
        item.classList.remove('active');
      }
    });

    this.updateButtonText(config);
  }

  /**
   * Lie les événements au dropdown
   * @param {Object} config - Configuration du dropdown
   */
  bindEvents(config) {
    config.items.forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.handleItemClick(config, item);
      });
    });
  }

  /**
   * Gère le clic sur un élément du dropdown
   * @param {Object} config - Configuration du dropdown
   * @param {HTMLElement} item - Élément cliqué
   */
  handleItemClick(config, item) {
    if (config.isMultiple) {
      this.toggleMultipleSelection(config, item);
    } else {
      this.setSingleSelection(config, item);
    }
    
    this.updateButtonText(config);
    this.triggerCallbacks(config);
  }

  /**
   * Gère la sélection multiple
   * @param {Object} config - Configuration du dropdown
   * @param {HTMLElement} item - Élément à basculer
   */
  toggleMultipleSelection(config, item) {
    const value = this.getItemValue(item);
    
    if (config.selectedItems.has(value)) {
      this.deselectItem(config, item);
    } else {
      this.selectItem(config, item);
    }
  }

  /**
   * Gère la sélection simple
   * @param {Object} config - Configuration du dropdown
   * @param {HTMLElement} item - Élément à sélectionner
   */
  setSingleSelection(config, item) {
    // Désélectionner tous les éléments
    config.items.forEach(el => {
      this.deselectItem(config, el, false);
    });

    // Sélectionner l'élément cliqué
    this.selectItem(config, item);
  }

  /**
   * Sélectionne un élément
   * @param {Object} config - Configuration du dropdown
   * @param {HTMLElement} item - Élément à sélectionner
   * @param {boolean} updateSet - Mettre à jour le Set des sélections
   */
  selectItem(config, item, updateSet = true) {
    const value = this.getItemValue(item);
    
    item.classList.add('active');
    item.setAttribute('aria-selected', 'true');
    
    if (updateSet) {
      config.selectedItems.add(value);
    }
  }

  /**
   * Désélectionne un élément
   * @param {Object} config - Configuration du dropdown
   * @param {HTMLElement} item - Élément à désélectionner
   * @param {boolean} updateSet - Mettre à jour le Set des sélections
   */
  deselectItem(config, item, updateSet = true) {
    const value = this.getItemValue(item);
    
    item.classList.remove('active');
    item.setAttribute('aria-selected', 'false');
    
    if (updateSet) {
      config.selectedItems.delete(value);
    }
  }
  /**
   * Met à jour le texte du bouton selon les sélections
   * @param {Object} config - Configuration du dropdown
   */
  updateButtonText(config) {
    const buttonText = config.button.querySelector('span, .btn-text') || config.button;
    const selectedItems = Array.from(config.selectedItems);
    
    // Vérifier si le bouton ne doit pas changer de texte
    const shouldChangeButtonText = config.button.hasAttribute('data-change-btn');
    if (!shouldChangeButtonText) {
      // Ne pas changer le texte du bouton, mais garder les classes
      if (selectedItems.length > 0) {
        config.button.classList.add('btn-active');
      } else {
        config.button.classList.remove('btn-active');
      }
      return;
    }
    
    if (selectedItems.length === 0) {
      const defaultText = config.button.getAttribute('data-default-text') || 'Sélectionner...';
      buttonText.textContent = defaultText;
      config.button.classList.remove('btn-active');
    } else if (config.isMultiple) {
      // Vérifier si un titre personnalisé est défini pour les sélections multiples
      const customTitle = config.button.getAttribute('data-title');
      
      if (selectedItems.length === 1) {
        const selectedItem = config.items.find(item => this.getItemValue(item) === selectedItems[0]);
        buttonText.textContent = this.getItemText(selectedItem);
      } else if (customTitle) {
        // Utiliser le titre personnalisé avec le nombre de sélections
        buttonText.textContent = `${customTitle} (${selectedItems.length})`;
      } else {
        // Utiliser le format par défaut
        buttonText.textContent = `${selectedItems.length} sélectionné(s)`;
      }
      config.button.classList.add('btn-active');
    } else {
      const selectedItem = config.items.find(item => this.getItemValue(item) === selectedItems[0]);
      if (selectedItem) {
        buttonText.textContent = this.getItemText(selectedItem);
      }
      config.button.classList.add('btn-active');
    }
  }

  /**
   * Récupère la valeur d'un élément
   * @param {HTMLElement} item - Élément
   * @returns {string} Valeur de l'élément
   */
  getItemValue(item) {
    return item.getAttribute('data-value') || 
           item.getAttribute('value') || 
           this.getItemText(item);
  }

  /**
   * Récupère le texte d'un élément
   * @param {HTMLElement} item - Élément
   * @returns {string} Texte de l'élément
   */
  getItemText(item) {
    return item.textContent.trim();
  }

  /**
   * Ajoute un callback pour les changements de sélection
   * @param {string} dropdownId - ID du dropdown
   * @param {Function} callback - Fonction de rappel
   */
  addCallback(dropdownId, callback) {
    const config = this.dropdowns.get(dropdownId);
    if (config && typeof callback === 'function') {
      config.callbacks.add(callback);
    }
  }

  /**
   * Déclenche tous les callbacks d'un dropdown
   * @param {Object} config - Configuration du dropdown
   */
  triggerCallbacks(config) {
    const selectedValues = Array.from(config.selectedItems);
    config.callbacks.forEach(callback => {
      try {
        callback(selectedValues, config.isMultiple);
      } catch (error) {
        console.error('Erreur dans le callback du dropdown:', error);
      }
    });
  }

  /**
   * Récupère les valeurs sélectionnées d'un dropdown
   * @param {string} dropdownId - ID du dropdown
   * @returns {Array} Valeurs sélectionnées
   */
  getSelectedValues(dropdownId) {
    const config = this.dropdowns.get(dropdownId);
    return config ? Array.from(config.selectedItems) : [];
  }

  /**
   * Définit les valeurs sélectionnées d'un dropdown
   * @param {string} dropdownId - ID du dropdown
   * @param {Array} values - Valeurs à sélectionner
   */
  setSelectedValues(dropdownId, values) {
    const config = this.dropdowns.get(dropdownId);
    if (!config) return;

    // Désélectionner tous les éléments
    config.items.forEach(item => {
      this.deselectItem(config, item, false);
    });
    config.selectedItems.clear();

    // Sélectionner les nouvelles valeurs
    values.forEach(value => {
      const item = config.items.find(item => this.getItemValue(item) === value);
      if (item) {
        this.selectItem(config, item);
      }
    });

    this.updateButtonText(config);
    this.triggerCallbacks(config);
  }

  /**
   * Réinitialise un dropdown
   * @param {string} dropdownId - ID du dropdown
   */
  reset(dropdownId) {
    this.setSelectedValues(dropdownId, []);
  }
}

// Instance globale du gestionnaire de dropdowns
window.dropdownManager = new DropdownManager();

/**
 * Initialise un menu déroulant pour le filtrage (rétrocompatibilité)
 * @param {string} btnId - ID du bouton du dropdown
 * @param {string} itemsSelector - Sélecteur pour les éléments du dropdown
 * @param {Function} filterCallback - Fonction de rappel pour le filtrage
 */
function initFilterDropdown(btnId, itemsSelector, filterCallback) {
  const dropdownBtn = document.getElementById(btnId);
  if (!dropdownBtn) return;
  
  const dropdown = dropdownBtn.closest('.dropdown');
  if (!dropdown) {
    console.warn('Le bouton doit être dans un élément .dropdown');
    return;
  }

  // Utiliser le nouveau gestionnaire
  const dropdownId = dropdown.id;
  if (dropdownId && typeof filterCallback === 'function') {
    window.dropdownManager.addCallback(dropdownId, (selectedValues, isMultiple) => {
      const value = isMultiple ? selectedValues : selectedValues[0];
      filterCallback(value || 'all');
    });
  }
}

/**
 * Initialise tous les dropdowns sur la page avec gestion automatique de la sélection multiple
 * Fonction générale qui détecte automatiquement les dropdowns et leur type
 */
function initAllDropdowns() {
  // Sélectionner tous les dropdowns
  const dropdowns = document.querySelectorAll('.dropdown');
  
  dropdowns.forEach(dropdown => {
    const isMultiple = dropdown.classList.contains('multiple');
    const trigger = dropdown.querySelector('[tabindex="0"][role="button"]');
    const items = dropdown.querySelectorAll('.dropdown-content a, .dropdown-content li > a');
    
    if (!trigger || !items.length) return;
    
    // Initialiser les états des éléments
    items.forEach(item => {
      if (!item.hasAttribute('aria-selected')) {
        item.setAttribute('aria-selected', 'false');
      }
    });
    
    // Ajouter les événements de clic
    items.forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isMultiple) {
          // Mode sélection multiple
          handleMultipleSelection(this, trigger, items);
        } else {
          // Mode sélection simple
          handleSingleSelection(this, trigger, items);
        }
      });
    });
  });
}

/**
 * Gère la sélection simple dans un dropdown
 * @param {HTMLElement} clickedItem - L'élément cliqué
 * @param {HTMLElement} trigger - Le bouton qui déclenche le dropdown
 * @param {NodeList} allItems - Tous les éléments du dropdown
 */
function handleSingleSelection(clickedItem, trigger, allItems) {
  // Désélectionner tous les autres éléments
  allItems.forEach(item => {
    item.classList.remove('active', 'selected');
    item.setAttribute('aria-selected', 'false');
  });
  
  // Sélectionner l'élément cliqué
  clickedItem.classList.add('active', 'selected');
  clickedItem.setAttribute('aria-selected', 'true');
  
  // Mettre à jour le texte du bouton si possible
  const triggerText = trigger.querySelector('span');
  if (triggerText) {
    triggerText.textContent = clickedItem.textContent.trim();
  }
  
  // Fermer le dropdown
  trigger.blur();
}

/**
 * Gère la sélection multiple dans un dropdown
 * @param {HTMLElement} clickedItem - L'élément cliqué
 * @param {HTMLElement} trigger - Le bouton qui déclenche le dropdown
 * @param {NodeList} allItems - Tous les éléments du dropdown
 */
function handleMultipleSelection(clickedItem, trigger, allItems) {
  // Basculer l'état de l'élément cliqué
  const isCurrentlySelected = clickedItem.getAttribute('aria-selected') === 'true';
  
  if (isCurrentlySelected) {
    // Désélectionner
    clickedItem.classList.remove('active', 'selected');
    clickedItem.setAttribute('aria-selected', 'false');
  } else {
    // Sélectionner
    clickedItem.classList.add('active', 'selected');
    clickedItem.setAttribute('aria-selected', 'true');
  }
  
  // Mettre à jour le texte du bouton avec le nombre de sélections
  updateMultipleSelectionText(trigger, allItems);
}

/**
 * Met à jour le texte du bouton pour refléter les sélections multiples
 * @param {HTMLElement} trigger - Le bouton qui déclenche le dropdown
 * @param {NodeList} allItems - Tous les éléments du dropdown
 */
function updateMultipleSelectionText(trigger, allItems) {
  const triggerText = trigger.querySelector('span');
  if (!triggerText) return;
  
  const selectedItems = Array.from(allItems).filter(item => 
    item.getAttribute('aria-selected') === 'true'
  );
  
  const originalText = triggerText.dataset.originalText || triggerText.textContent;
  
  // Stocker le texte original s'il n'existe pas déjà
  if (!triggerText.dataset.originalText) {
    triggerText.dataset.originalText = originalText;
  }
  
  if (selectedItems.length === 0) {
    triggerText.textContent = originalText;
    trigger.classList.remove('dropdown-has-selection');
  } else if (selectedItems.length === 1) {
    triggerText.textContent = selectedItems[0].textContent.trim();
    trigger.classList.add('dropdown-has-selection');
  } else {
    triggerText.textContent = `${originalText} (${selectedItems.length})`;
    trigger.classList.add('dropdown-has-selection');
  }
}

/**
 * Récupère les valeurs sélectionnées d'un dropdown
 * @param {HTMLElement} dropdown - L'élément dropdown
 * @returns {Array} Tableau des valeurs sélectionnées
 */
function getDropdownSelectedValues(dropdown) {
  const items = dropdown.querySelectorAll('.dropdown-content a[aria-selected="true"]');
  return Array.from(items).map(item => ({
    value: item.getAttribute('data-value') || item.textContent.trim(),
    text: item.textContent.trim(),
    element: item
  }));
}

/**
 * Réinitialise toutes les sélections d'un dropdown
 * @param {HTMLElement} dropdown - L'élément dropdown à réinitialiser
 */
function resetDropdownSelection(dropdown) {
  const trigger = dropdown.querySelector('[tabindex="0"][role="button"]');
  const items = dropdown.querySelectorAll('.dropdown-content a');
  const triggerText = trigger?.querySelector('span');
  
  // Désélectionner tous les éléments
  items.forEach(item => {
    item.classList.remove('active', 'selected');
    item.setAttribute('aria-selected', 'false');
  });
  
  // Restaurer le texte original du bouton
  if (triggerText && triggerText.dataset.originalText) {
    triggerText.textContent = triggerText.dataset.originalText;
    trigger.classList.remove('dropdown-has-selection');
  }
}

/**
 * Réinitialise tous les dropdowns de la page
 */
function resetAllDropdowns() {
  const dropdowns = document.querySelectorAll('.dropdown');
  dropdowns.forEach(dropdown => {
    resetDropdownSelection(dropdown);
  });
}

/**
 * Initialise automatiquement tous les dropdowns quand le DOM est chargé
 */
document.addEventListener('DOMContentLoaded', function() {
  initAllDropdowns();
  
  // Gérer les boutons de réinitialisation s'ils existent
  const resetButtons = document.querySelectorAll('[data-reset-filters]');
  resetButtons.forEach(button => {
    button.addEventListener('click', function() {
      resetAllDropdowns();
    });
  });
});