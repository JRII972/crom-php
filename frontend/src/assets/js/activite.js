/**
 * Scripts spécifiques à la page d'une activite individuelle
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialiser la gestion des activites et sessions
  initActiviteHandlers();
});

/**
 * Initialise les gestionnaires pour la activite et les sessions
 */
function initActiviteHandlers() {
  updateUIForActiviteType();
  handleInscriptionActivite();
  handleInscriptionSession();
}

/**
 * Met à jour l'interface en fonction du type de activite
 */
function updateUIForActiviteType() {
  // Dans un contexte réel, vous récupéreriez ces informations depuis l'API
  const typeActivite = "CAMPAGNE"; // ou "ONESHOT", "JEU_DE_SOCIETE", "EVENEMENT"
  const typeCampagne = "FERMEE"; // ou "OUVERTE" ou null si pas une campagne
  
  // Éléments conditionnels
  const campagneInfoContainer = document.getElementById('campagne-info');
  const joueursContainer = document.getElementById('activite-joueurs-container');
  const inscriptionContainer = document.getElementById('activite-inscription-container');
  
  if (!campagneInfoContainer || !joueursContainer || !inscriptionContainer) return;
  
  // Afficher/masquer les éléments en fonction du type
  if (typeActivite === "CAMPAGNE") {
    // C'est une campagne, on affiche les infos supplémentaires
    campagneInfoContainer.style.display = "block";
    joueursContainer.style.display = "block";
    inscriptionContainer.style.display = "block";
    
    // Mettre à jour le badge du type de campagne
    const badgeCampagne = campagneInfoContainer.querySelector('.badge');
    if (badgeCampagne) {
      badgeCampagne.textContent = `Campagne ${typeCampagne === "FERMEE" ? "Fermée" : "Ouverte"}`;
    }
    
    // Pour une campagne fermée, vérifier si le nombre maximum de joueurs est atteint
    if (typeCampagne === "FERMEE") {
      const btnInscription = document.getElementById('btn-inscription-activite');
      const joueurActuel = 3; // Simulation du nombre actuel de joueurs
      const maxJoueurs = 5;   // Simulation du nombre max de joueurs
      
      if (btnInscription && joueurActuel >= maxJoueurs) {
        btnInscription.classList.add('btn-disabled');
        btnInscription.textContent = "Campagne complète";
      }
    }
  } else {
    // Ce n'est pas une campagne, on masque les éléments spécifiques
    campagneInfoContainer.style.display = "none";
    joueursContainer.style.display = "none";
    inscriptionContainer.style.display = "none";
  }
  
  // Mettre à jour le badge du type de activite
  const badgeType = document.getElementById('activite-type');
  if (badgeType) {
    const typeMap = {
      "CAMPAGNE": "Campagne",
      "ONESHOT": "One-Shot",
      "JEU_DE_SOCIETE": "Jeu de Société",
      "EVENEMENT": "Événement"
    };
    badgeType.textContent = typeMap[typeActivite] || typeActivite;
  }
}

/**
 * Gère l'inscription à une activite
 */
function handleInscriptionActivite() {
  const btnInscription = document.getElementById('btn-inscription-activite');
  if (!btnInscription) return;
  
  btnInscription.addEventListener('click', function() {
    // Simuler une inscription
    alert("Vous êtes maintenant inscrit à cette campagne!");
    
    // Dans un contexte réel, vous enverriez une requête à l'API puis mettriez à jour l'interface
    this.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
      </svg>
      Inscrit à la campagne
    `;
    this.classList.remove('btn-primary');
    this.classList.add('btn-success');
    
    // Mettre à jour le nombre de joueurs inscrits
    const placesElement = document.getElementById('activite-places');
    if (placesElement) {
      placesElement.textContent = "4 places sur 5 disponibles";
    }
  });
}

/**
 * Gère l'inscription à une session
 */
function handleInscriptionSession() {
  const btnInscriptionSession = document.querySelectorAll('.card-body .btn-outline.btn-primary');
  
  btnInscriptionSession.forEach(btn => {
    btn.addEventListener('click', function() {
      const typeActivite = "CAMPAGNE"; // Simulation
      const estMembreCampagne = false; // Simulation - si l'utilisateur est membre de la campagne
      
      if (typeActivite === "CAMPAGNE" && !estMembreCampagne) {
        // Pour les campagnes, vérifier si l'utilisateur est membre
        alert("Vous devez d'abord vous inscrire à la campagne avant de pouvoir participer à une session.");
        return;
      }
      
      // Simuler une inscription à la session
      alert("Vous êtes maintenant inscrit à cette session!");
      
      // Dans un contexte réel, vous enverriez une requête à l'API puis mettriez à jour l'interface
      this.innerHTML = "Inscrit";
      this.classList.remove('btn-primary', 'btn-outline');
      this.classList.add('btn-success');
      
      // Mettre à jour le nombre de joueurs inscrits pour cette session
      const placesContainer = this.parentElement.querySelector('.text-sm');
      if (placesContainer) {
        const [current, max] = placesContainer.querySelector('span').textContent.split('/');
        const newCurrent = parseInt(current) + 1;
        placesContainer.querySelector('span').textContent = `${newCurrent}/${max}`;
      }
      
      // Ajouter le joueur à la liste des participants
      const participantsContainer = this.closest('.card-body').querySelector('.flex-wrap.gap-2');
      if (participantsContainer) {
        // Vérifier s'il y a un message "Aucun participant"
        const emptyMessage = participantsContainer.querySelector('.text-xs');
        if (emptyMessage && emptyMessage.textContent === "Aucun participant inscrit") {
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
          <span class="text-xs">Thomas Dubois (@TomD)</span>
        `;
        participantsContainer.appendChild(newParticipant);
      }
    });
  });
}