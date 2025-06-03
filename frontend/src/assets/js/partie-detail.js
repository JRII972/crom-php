/**
 * Scripts spécifiques à la page de détail d'une partie
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialiser les onglets
  initTabs({
    'tab-description': 'tab-content-description',
    'tab-sessions': 'tab-content-sessions'
  });

  // Initialiser la gestion des parties
  initPartieUI();
});

/**
 * Initialise l'interface utilisateur pour les parties
 */
function initPartieUI() {
  // Variables pour démo uniquement - Ces valeurs seraient récupérées de l'API
  let partieData = {
    id: 1,
    typePartie: 'CAMPAGNE', // CAMPAGNE, ONESHOT, JEU_DE_SOCIETE, EVENEMENT
    typeCampagne: 'OUVERTE', // OUVERTE, FERMEE (null pour non-campagnes)
    estInscrit: false, // L'utilisateur actuel est-il inscrit à cette partie?
    placesMax: 5,
    placesRestantes: 2,
  };
  
  updateUIBasedOnPartieType(partieData);
  
  // Gestion du bouton d'inscription à la partie
  const inscriptionBtn = document.getElementById('inscription-partie-btn');
  if (inscriptionBtn) {
    inscriptionBtn.addEventListener('click', function() {
      // Simuler une inscription/désinscription
      partieData.estInscrit = !partieData.estInscrit;
      
      // Si l'utilisateur s'inscrit, diminuer les places restantes
      if (partieData.estInscrit) {
        partieData.placesRestantes--;
      } else {
        partieData.placesRestantes++;
      }
      
      // Mettre à jour l'interface
      updateUIBasedOnPartieType(partieData);
    });
  }
  
  // Gestion des boutons d'inscription aux sessions
  initSessionRegistration(partieData);
}

/**
 * Met à jour l'interface en fonction du type de partie
 * @param {Object} partieData - Les données de la partie
 */
function updateUIBasedOnPartieType(partieData) {
  const typePartie = partieData.typePartie;
  const typeCampagne = partieData.typeCampagne;
  const estInscrit = partieData.estInscrit;
  
  const inscriptionBtn = document.getElementById('inscription-partie-btn');
  const inscriptionRequiredAlert = document.getElementById('inscription-required-alert');
  const campagneCompleteAlert = document.getElementById('campagne-complete-alert');
  const joueursPartieContainer = document.getElementById('partie-joueurs-container');
  const campagneInfo = document.getElementById('campagne-info');
  
  if (!inscriptionBtn || !inscriptionRequiredAlert || !campagneCompleteAlert) return;
  
  // Affichage du badge de type
  const partieBadge = document.getElementById('partie-type-badge');
  if (partieBadge) {
    partieBadge.textContent = 
      typePartie === 'CAMPAGNE' ? 'Campagne' :
      typePartie === 'ONESHOT' ? 'One-Shot' :
      typePartie === 'JEU_DE_SOCIETE' ? 'Jeu de Société' : 'Événement';
  }
  
  // Mise à jour des informations de campagne
  if (typePartie === 'CAMPAGNE') {
    // Afficher les éléments spécifiques aux campagnes
    if (campagneInfo) campagneInfo.classList.remove('hidden');
    if (inscriptionRequiredAlert) inscriptionRequiredAlert.classList.remove('hidden');
    if (joueursPartieContainer) joueursPartieContainer.classList.remove('hidden');
    
    // Afficher le badge de type de campagne
    const typeCampagneBadge = document.getElementById('type-campagne-badge');
    if (typeCampagneBadge) {
      typeCampagneBadge.textContent = 
        typeCampagne === 'FERMEE' ? 'Campagne Fermée' : 'Campagne Ouverte';
    }
    
    // Vérifier si la campagne est complète (pour les campagnes fermées)
    if (typeCampagne === 'FERMEE' && partieData.placesRestantes <= 0) {
      campagneCompleteAlert.classList.remove('hidden');
      inscriptionBtn.classList.add('btn-disabled');
      inscriptionBtn.textContent = 'Campagne complète';
    } else {
      campagneCompleteAlert.classList.add('hidden');
      
      // Mettre à jour le bouton d'inscription
      if (estInscrit) {
        inscriptionBtn.classList.remove('btn-primary');
        inscriptionBtn.classList.add('btn-error');
        inscriptionBtn.textContent = 'Se désinscrire de cette campagne';
      } else {
        inscriptionBtn.classList.add('btn-primary');
        inscriptionBtn.classList.remove('btn-error');
        inscriptionBtn.textContent = 'S\'inscrire à cette campagne';
      }
    }
  } else {
    // Pour les One-Shot, Jeux de Société et Événements
    if (campagneInfo) campagneInfo.classList.add('hidden');
    if (inscriptionRequiredAlert) inscriptionRequiredAlert.classList.add('hidden');
    if (joueursPartieContainer) joueursPartieContainer.classList.add('hidden');
    
    // Cacher le bouton d'inscription à la campagne
    const inscriptionContainer = document.getElementById('inscription-partie-container');
    if (inscriptionContainer) inscriptionContainer.classList.add('hidden');
  }
  
  // Mise à jour du badge d'information sur les places
  const placeBadge = document.getElementById('partie-place-badge');
  if (placeBadge) {
    placeBadge.textContent = 
      `${partieData.placesMax - partieData.placesRestantes}/${partieData.placesMax} places`;
  }
}

/**
 * Initialise l'inscription aux sessions
 * @param {Object} partieData - Les données de la partie
 */
function initSessionRegistration(partieData) {
  const sessionButtons = document.querySelectorAll('.btn-primary.btn-sm');
  
  sessionButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Vérifier si l'utilisateur est inscrit à la campagne pour les campagnes
      if (partieData.typePartie === 'CAMPAGNE' && !partieData.estInscrit) {
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