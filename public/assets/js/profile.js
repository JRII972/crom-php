/**
 * Scripts spécifiques à la page de profil
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialiser les onglets
  initTabs({
    'tab-parties': 'tab-content-parties',
    'tab-disponibilites': 'tab-content-disponibilites',
    'tab-historique': 'tab-content-historique',
    'tab-preference': 'tab-content-preference',
    'tab-paiements': 'tab-content-paiements'
  });
  
  // Initialiser les fonctionnalités du profil
  initProfileActions();
});

/**
 * Initialise les actions liées au profil utilisateur
 */
function initProfileActions() {
  // Gestionnaire pour le bouton d'édition du profil
  const editProfileBtn = document.getElementById('edit-profile-btn');
  if (editProfileBtn) {
    editProfileBtn.addEventListener('click', () => {
      const modal = document.getElementById('edit-profile-modal');
      if (modal) modal.showModal();
    });
  }
  
  // Gestionnaire pour le bouton d'annulation
  const cancelEditBtn = document.getElementById('cancel-edit-btn');
  if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', () => {
      const modal = document.getElementById('edit-profile-modal');
      if (modal) modal.close();
    });
  }
  
  // Gestionnaire pour le bouton de sauvegarde
  const saveProfileBtn = document.getElementById('save-profile-btn');
  if (saveProfileBtn) {
    saveProfileBtn.addEventListener('click', () => {
      // Simuler une sauvegarde (dans un contexte réel, cela enverrait les données au backend)
      const firstName = document.getElementById('edit-firstname').value;
      const lastName = document.getElementById('edit-lastname').value;
      const pseudo = document.getElementById('edit-pseudo').value;
      const email = document.getElementById('edit-email').value;
      
      // Mettre à jour l'interface utilisateur avec les nouvelles valeurs
      updateProfileUI(firstName, lastName, pseudo, email);
      
      // Fermer la modal
      const modal = document.getElementById('edit-profile-modal');
      if (modal) modal.close();
      
      // Afficher un toast de confirmation
      alert('Profil mis à jour avec succès!');
    });
  }
  
  // Gestion du changement de photo de profil
  const changePhotoBtn = document.getElementById('change-photo-btn');
  if (changePhotoBtn) {
    changePhotoBtn.addEventListener('click', () => {
      // Simuler une ouverture de sélecteur de fichier
      alert('Cette fonctionnalité permettrait de télécharger une nouvelle photo de profil');
    });
  }
}

/**
 * Met à jour l'interface utilisateur du profil
 * @param {string} firstName - Prénom
 * @param {string} lastName - Nom
 * @param {string} pseudo - Pseudonyme
 * @param {string} email - Email
 */
function updateProfileUI(firstName, lastName, pseudo, email) {
  // Mettre à jour les éléments de la page
  const fullNameElement = document.getElementById('profile-full-name');
  const pseudoElement = document.getElementById('profile-pseudo');
  const emailElement = document.getElementById('profile-email');
  const sidebarNameElement = document.getElementById('sidebar-user-name');
  const sidebarEmailElement = document.getElementById('sidebar-user-email');
  
  if (fullNameElement) fullNameElement.textContent = `${firstName} ${lastName}`;
  if (pseudoElement) pseudoElement.textContent = `@${pseudo}`;
  if (emailElement) emailElement.textContent = email;
  
  // Mettre à jour la barre latérale
  if (sidebarNameElement) sidebarNameElement.textContent = `${firstName} ${lastName}`;
  if (sidebarEmailElement) sidebarEmailElement.textContent = email;
}