/**
 * Scripts spécifiques à la page de détail d'une activite
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialiser les onglets avec le nouvel onglet "Détail"
  initTabs({
    'tab-detail': 'tab-content-detail',
    'tab-description': 'tab-content-description',
    'tab-sessions': 'tab-content-sessions',
    'tab-gestion-joueurs': 'tab-content-gestion-joueurs',
    'tab-gestion-sessions': 'tab-content-gestion-sessions',
  });

  // Gérer l'affichage responsive des onglets
  initResponsiveTabs();

  // Initialiser la gestion des activites
  initActiviteUI();
});

/**
 * Affiche ou masque l'indicateur de chargement
 * @param {boolean} show - Indique si l'indicateur doit être affiché
 */
function showLoading(show) {
  const $submitButtons = $('#inscription-activite-btn');
  
  if ($submitButtons.length) {
    if (show) {
      $submitButtons.prop('disabled', true)
        .html('<span class="loading loading-spinner loading-sm"></span> Inscription...');
    } else {
      $submitButtons.prop('disabled', false)
        .text('Se connecter');
    }
  }
}

/**
 * Initialise l'affichage responsive des onglets
 */
function initResponsiveTabs() {
  function setActiveTab() {
    const isDesktop = window.innerWidth >= 768; // md breakpoint
    const tabDetail = document.getElementById('tab-detail');
    const tabDescription = document.getElementById('tab-description');
    const tabSessions = document.getElementById('tab-sessions');
    const contentDetail = document.getElementById('tab-content-detail');
    const contentDescription = document.getElementById('tab-content-description');
    const contentSessions = document.getElementById('tab-content-sessions');

    if (isDesktop) {
      // Sur desktop, l'onglet Description est actif par défaut
      if (tabDetail) tabDetail.classList.remove('tab-active');
      if (tabDescription) tabDescription.classList.add('tab-active');
      if (tabSessions) tabSessions.classList.remove('tab-active');
      
      if (contentDetail) contentDetail.classList.add('hidden');
      if (contentDescription) contentDescription.classList.remove('hidden');
      if (contentSessions) contentSessions.classList.add('hidden');
    } else {
      // Sur mobile, l'onglet Détail est actif par défaut
      if (tabDetail) tabDetail.classList.add('tab-active');
      if (tabDescription) tabDescription.classList.remove('tab-active');
      if (tabSessions) tabSessions.classList.remove('tab-active');
      
      if (contentDetail) contentDetail.classList.remove('hidden');
      if (contentDescription) contentDescription.classList.add('hidden');
      if (contentSessions) contentSessions.classList.add('hidden');
    }
  }

  // Définir l'onglet actif au chargement
  setActiveTab();

  // Écouter les changements de taille d'écran
  window.addEventListener('resize', setActiveTab);
}

/**
 * Initialise l'interface utilisateur pour les activites
 */
function initActiviteUI() {
  // Variables pour démo uniquement - Ces valeurs seraient récupérées de l'API
  let activiteData = {
    id: 1,
    typeActivite: 'CAMPAGNE', // CAMPAGNE, ONESHOT, JEU_DE_SOCIETE, EVENEMENT
    typeCampagne: 'OUVERTE', // OUVERTE, FERMEE (null pour non-campagnes)
    estInscrit: false, // L'utilisateur actuel est-il inscrit à cette activite?
    placesMax: 5,
    placesRestantes: 2,
  };
    updateUIBasedOnActiviteType(activiteData);
  
  // Gestion des boutons d'inscription à la activite (desktop et mobile)
  const inscriptionBtns = document.querySelectorAll('#inscription-activite-btn');
  inscriptionBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      showLoading(true);
      // Simuler une inscription/désinscription
      activiteData.estInscrit = !activiteData.estInscrit;
      
      // Si l'utilisateur s'inscrit, diminuer les places restantes
      if (activiteData.estInscrit) {
        activiteData.placesRestantes--;
      } else {
        activiteData.placesRestantes++;
      }
      
      // Mettre à jour l'interface
      updateUIBasedOnActiviteType(activiteData);
      showLoading(false);
    });
  });
  
  // Gestion des boutons d'inscription aux sessions
  initSessionRegistration(activiteData);
}

/**
 * Met à jour l'interface en fonction du type de activite
 * @param {Object} activiteData - Les données de la activite
 */
function updateUIBasedOnActiviteType(activiteData) {
  const typeActivite = activiteData.typeActivite;
  const typeCampagne = activiteData.typeCampagne;
  const estInscrit = activiteData.estInscrit;
  
  const inscriptionBtns = document.querySelectorAll('#inscription-activite-btn');
  const inscriptionRequiredAlert = document.getElementById('inscription-required-alert');
  const campagneCompleteAlert = document.getElementById('campagne-complete-alert');
  const joueursActiviteContainer = document.getElementById('activite-joueurs-container');
  const campagneInfo = document.getElementById('campagne-info');
  
  if (inscriptionBtns.length === 0 || !inscriptionRequiredAlert || !campagneCompleteAlert) return;
  
  // Affichage du badge de type
  const activiteBadge = document.getElementById('activite-type-badge');
  if (activiteBadge) {
    activiteBadge.textContent = 
      typeActivite === 'CAMPAGNE' ? 'Campagne' :
      typeActivite === 'ONESHOT' ? 'One-Shot' :
      typeActivite === 'JEU_DE_SOCIETE' ? 'Jeu de Société' : 'Événement';
  }
  
  // Mise à jour des informations de campagne
  if (typeActivite === 'CAMPAGNE') {
    // Afficher les éléments spécifiques aux campagnes
    if (campagneInfo) campagneInfo.classList.remove('hidden');
    if (inscriptionRequiredAlert) inscriptionRequiredAlert.classList.remove('hidden');
    if (joueursActiviteContainer) joueursActiviteContainer.classList.remove('hidden');
    
    // Afficher le badge de type de campagne
    const typeCampagneBadge = document.getElementById('type-campagne-badge');
    if (typeCampagneBadge) {
      typeCampagneBadge.textContent = 
        typeCampagne === 'FERMEE' ? 'Campagne Fermée' : 'Campagne Ouverte';
    }
    
    // Vérifier si la campagne est complète (pour les campagnes fermées)
    if (typeCampagne === 'FERMEE' && activiteData.placesRestantes <= 0) {
      campagneCompleteAlert.classList.remove('hidden');
      inscriptionBtns.forEach(btn => {
        btn.classList.add('btn-disabled');
        btn.textContent = 'Campagne complète';
      });
    } else {
      campagneCompleteAlert.classList.add('hidden');
      
      // Mettre à jour les boutons d'inscription
      inscriptionBtns.forEach(btn => {
        if (estInscrit) {
          btn.classList.remove('btn-primary');
          btn.classList.add('btn-error');
          btn.textContent = 'Se désinscrire de cette campagne';
        } else {
          btn.classList.add('btn-primary');
          btn.classList.remove('btn-error');
          btn.textContent = 'S\'inscrire à cette campagne';
        }
      });
    }
  } else {
    // Pour les One-Shot, Jeux de Société et Événements
    if (campagneInfo) campagneInfo.classList.add('hidden');
    if (inscriptionRequiredAlert) inscriptionRequiredAlert.classList.add('hidden');
    if (joueursActiviteContainer) joueursActiviteContainer.classList.add('hidden');
    
    // Cacher les conteneurs d'inscription à la campagne
    const inscriptionContainers = document.querySelectorAll('.inscription-activite-container');
    inscriptionContainers.forEach(container => container.classList.add('hidden'));
  }
  
  // Mise à jour du badge d'information sur les places
  const placeBadge = document.getElementById('activite-place-badge');
  if (placeBadge) {
    placeBadge.textContent = 
      `${activiteData.placesMax - activiteData.placesRestantes}/${activiteData.placesMax} places`;
  }
}

/**
 * Initialise l'inscription aux sessions
 * @param {Object} activiteData - Les données de la activite
 */
function initSessionRegistration(activiteData) {
  const sessionButtons = document.querySelectorAll('.btn-primary.btn-sm');
  
  sessionButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Vérifier si l'utilisateur est inscrit à la campagne pour les campagnes
      if (activiteData.typeActivite === 'CAMPAGNE' && !activiteData.estInscrit) {
        alert('Vous devez d\'abord vous inscrire à la campagne pour pouvoir participer aux sessions.');
        return;
      }
      
      // Traitement de l'inscription à la session
      const sessionElement = this.closest('.card');
      const placesElement = sessionElement.querySelector('.badge-primary');
      if (!placesElement) return;
      
      const placesText = placesElement.textContent;
      const [current, max] = placesText.split('/').map(n => parseInt(n.trim()));
      
      if (this.textContent === 'S\'inscrire') {
        // S'inscrire
        if (current < max) {
          placesElement.textContent = `${current + 1}/${max} places`;
          this.textContent = 'Se désinscrire';
          this.classList.remove('btn-primary');
          this.classList.add('btn-error');
          
          // Mise à jour de la liste des participants
          updateParticipantsList(sessionElement, true);
        }
      } else {
        // Se désinscrire
        placesElement.textContent = `${current - 1}/${max} places`;
        this.textContent = 'S\'inscrire';
        this.classList.add('btn-primary');
        this.classList.remove('btn-error');
        
        // Mise à jour de la liste des participants
        updateParticipantsList(sessionElement, false);
      }
    });
  });
}

/**
 * Met à jour la liste des participants d'une session
 * @param {Element} sessionElement - L'élément DOM de la session
 * @param {boolean} isRegistering - True si inscription, false si désinscription
 */
function updateParticipantsList(sessionElement, isRegistering) {
  const participantsContainer = sessionElement.querySelector('.flex-wrap.gap-2');
  if (!participantsContainer) return;
  
  if (isRegistering) {
    // Vérifier s'il y a un message "Aucun participant"
    const emptyMessage = participantsContainer.querySelector('.italic');
    if (emptyMessage) {
      participantsContainer.innerHTML = '';
    }
    
    // Ajouter le nouveau participant
    const newParticipant = document.createElement('div');
    newParticipant.className = "flex items-center gap-2 bg-base-200 rounded-full py-1 px-3";
    newParticipant.innerHTML = `
      <div class="avatar">
        <div class="w-6 h-6 rounded-full">
          <img src="https://picsum.photos/203" alt="Avatar joueur" />
        </div>
      </div>
      <span class="text-sm">Thomas Dubois <span class="text-xs opacity-70">@TomD</span></span>
    `;
    participantsContainer.appendChild(newParticipant);
  } else {
    // Supprimer le dernier participant (simplification)
    const participants = participantsContainer.querySelectorAll('.flex.items-center.gap-2');
    if (participants.length > 0) {
      participants[participants.length - 1].remove();
    }
      // Si plus de participants, afficher un message
    if (participants.length <= 1) {
      participantsContainer.innerHTML = '<p class="text-sm italic">Aucun joueur inscrit pour le moment.</p>';
    }
  }
}

/**
 * Gestionnaire pour le modal d'ajout de session
 */
class SessionModalManager {
  constructor() {
    this.selectizeInstance = null;
    this.lieuxData = [
      {
        id: 'paris-lbdr',
        name: 'Salle Paris - Local LBDR',
        address: '15 rue de la République, 75003 Paris',
        heureDebut: '19:00',
        heureFin: '23:00',
        capaciteMax: 8
      },
      {
        id: 'lyon-centre',
        name: 'Salle Lyon Centre',
        address: '45 cours Vitton, 69006 Lyon',
        heureDebut: '18:30',
        heureFin: '22:30',
        capaciteMax: 6
      },
      {
        id: 'marseille-vieux-port',
        name: 'Local Marseille Vieux-Port',
        address: '23 quai du Port, 13002 Marseille',
        heureDebut: '19:30',
        heureFin: '23:30',
        capaciteMax: 10
      },
      {
        id: 'toulouse-capitole',
        name: 'Salle Toulouse Capitole',
        address: '8 place du Capitole, 31000 Toulouse',
        heureDebut: '18:00',
        heureFin: '22:00',
        capaciteMax: 5
      },
      {
        id: 'bordeaux-chartrons',
        name: 'Local Bordeaux Chartrons',
        address: '34 cours Portal, 33000 Bordeaux',
        heureDebut: '19:15',
        heureFin: '23:15',
        capaciteMax: 7
      }
    ];
    
    this.init();
  }
    init() {
    // Initialiser les éléments DOM
    this.titreInput = document.getElementById('session-titre');
    this.dateInput = document.getElementById('session-date');
    this.lieuSelect = document.getElementById('session-lieu');
    this.heureContainer = document.getElementById('session-heure-container');
    this.heureDisplay = document.getElementById('session-heure-display');
    this.maxJoueursInput = document.getElementById('session-max-joueurs');
    this.form = document.getElementById('form-ajouter-session');
    
    // Vérifier que tous les éléments existent
    if (!this.titreInput || !this.dateInput || !this.lieuSelect || !this.heureContainer || !this.heureDisplay || !this.form) {
      console.warn('SessionModalManager: Certains éléments DOM sont manquants');
      return;
    }
    
    this.setupEventListeners();
    this.initializeSelectize();
  }
  
  setupEventListeners() {
    // Écouteur pour le changement de date
    this.dateInput.addEventListener('change', () => {
      this.onDateChange();
    });
    
    // Écouteur pour la soumission du formulaire
    this.form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.onFormSubmit();
    });
  }
    initializeSelectize() {
    // Vérifier si Selectize.js est disponible
    if (typeof Selectize === 'undefined') {
      console.warn('Selectize.js n\'est pas chargé');
      return;
    }
    
    // Initialiser Selectize.js en mode vanilla (sans jQuery)
    if (this.lieuSelect) {
      try {
        this.selectizeInstance = new Selectize(this.lieuSelect, {
          valueField: 'id',
          labelField: 'name',
          searchField: ['name', 'address'],
          placeholder: 'Sélectionnez d\'abord une date...',
          options: [],
          render: {
            option: (item, escape) => {
              return `
                <div style="padding: 8px;">
                  <div style="font-weight: 600;">${escape(item.name)}</div>
                  <div style="font-size: 0.875rem; color: #666;">${escape(item.address)}</div>
                  <div style="font-size: 0.75rem; color: #3b82f6;">${escape(item.heureDebut)} - ${escape(item.heureFin)} • Max ${item.capaciteMax} joueurs</div>
                </div>
              `;
            },
            item: (item, escape) => {
              return `<div>${escape(item.name)}</div>`;
            }
          },
          onChange: (value) => {
            this.onLieuChange(value);
          }
        });
      } catch (error) {
        console.error('Erreur lors de l\'initialisation de Selectize:', error);
        // Fallback vers un select normal si Selectize échoue
        this.lieuSelect.addEventListener('change', (e) => {
          this.onLieuChange(e.target.value);
        });
      }
    }
  }
    onDateChange() {
    const selectedDate = this.dateInput.value;
    
    if (selectedDate) {
      // Activer le sélecteur de lieu
      if (this.selectizeInstance) {
        this.selectizeInstance.enable();
        this.selectizeInstance.clearOptions();
        this.selectizeInstance.addOption(this.lieuxData);
        this.selectizeInstance.setValue('');
        this.selectizeInstance.updatePlaceholder('Choisissez un lieu...');
      } else {
        // Fallback pour select HTML normal
        this.lieuSelect.disabled = false;
        this.lieuSelect.innerHTML = '<option value="">Choisissez un lieu...</option>';
        this.lieuxData.forEach(lieu => {
          const option = document.createElement('option');
          option.value = lieu.id;
          option.textContent = `${lieu.name} (${lieu.heureDebut}-${lieu.heureFin})`;
          this.lieuSelect.appendChild(option);
        });
      }
      
      // Masquer l'affichage de l'heure
      this.heureContainer.classList.add('hidden');
    } else {
      // Désactiver le sélecteur de lieu
      if (this.selectizeInstance) {
        this.selectizeInstance.disable();
        this.selectizeInstance.clear();
        this.selectizeInstance.clearOptions();
        this.selectizeInstance.updatePlaceholder('Sélectionnez d\'abord une date...');
      } else {
        // Fallback pour select HTML normal
        this.lieuSelect.disabled = true;
        this.lieuSelect.innerHTML = '<option value="">Sélectionnez d\'abord une date...</option>';
      }
      
      // Masquer l'affichage de l'heure
      this.heureContainer.classList.add('hidden');
    }
  }
  
  onLieuChange(lieuId) {
    if (lieuId) {
      const lieu = this.lieuxData.find(l => l.id === lieuId);
      if (lieu) {
        // Afficher les heures automatiques
        this.heureDisplay.textContent = `${lieu.heureDebut} - ${lieu.heureFin}`;
        this.heureContainer.classList.remove('hidden');
        
        // Mettre à jour le nombre max de joueurs par défaut
        this.maxJoueursInput.value = Math.min(lieu.capaciteMax, 5);
        this.maxJoueursInput.max = lieu.capaciteMax;
      }
    } else {
      // Masquer l'affichage de l'heure
      this.heureContainer.classList.add('hidden');
    }
  }
    onFormSubmit() {
    const formData = new FormData(this.form);
    const sessionData = {
      titre: formData.get('titre'),
      date: formData.get('date'),
      lieu: formData.get('lieu'),
      maxJoueurs: formData.get('max_joueurs'),
      notes: formData.get('notes')
    };
    
    // Validation
    if (!sessionData.titre || !sessionData.date || !sessionData.lieu) {
      alert('Veuillez remplir tous les champs obligatoires (titre, date et lieu).');
      return;
    }
    
    // Validation supplémentaire pour le titre
    if (sessionData.titre.trim().length < 3) {
      alert('Le titre de la session doit contenir au moins 3 caractères.');
      return;
    }
    
    // Obtenir les détails du lieu sélectionné
    const lieu = this.lieuxData.find(l => l.id === sessionData.lieu);
    if (lieu) {
      sessionData.heureDebut = lieu.heureDebut;
      sessionData.heureFin = lieu.heureFin;
      sessionData.lieuNom = lieu.name;
      sessionData.lieuAdresse = lieu.address;
    }
      // Simuler la création de la session
    console.log('Nouvelle session créée:', sessionData);
    
    // Afficher un message de succès
    alert(`Session "${sessionData.titre}" créée avec succès pour le ${sessionData.date} à ${sessionData.lieuNom} de ${sessionData.heureDebut} à ${sessionData.heureFin}`);
    
    // Fermer le modal
    document.getElementById('modal-ajouter-session').close();
    
    // Réinitialiser le formulaire
    this.resetForm();
    
    // TODO: Ici on ajouterait la nouvelle session à la liste des sessions affichées
    // this.addSessionToUI(sessionData);
  }
    resetForm() {
    this.form.reset();
    if (this.selectizeInstance) {
      this.selectizeInstance.disable();
      this.selectizeInstance.clear();
      this.selectizeInstance.clearOptions();
      this.selectizeInstance.updatePlaceholder('Sélectionnez d\'abord une date...');
    } else {
      // Fallback pour select HTML normal
      this.lieuSelect.disabled = true;
      this.lieuSelect.innerHTML = '<option value="">Sélectionnez d\'abord une date...</option>';
    }
    this.heureContainer.classList.add('hidden');
  }
}

// Initialiser le gestionnaire de modal de session quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
  console.log('Initialisation du gestionnaire de session...');
  
  // Attendre un peu pour s'assurer que Selectize.js est chargé
  setTimeout(() => {
    try {
      const sessionManager = new SessionModalManager();
      console.log('SessionModalManager initialisé avec succès');
    } catch (error) {
      console.error('Erreur lors de l\'initialisation du SessionModalManager:', error);
    }
  }, 100);
});