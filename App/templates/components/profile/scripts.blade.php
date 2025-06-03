{{-- Scripts spécifiques à la page de profil --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabs = document.querySelectorAll('[role="tab"]');
    const tabContents = document.querySelectorAll('[id^="tab-content-"]');
    
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Désactive tous les onglets
        tabs.forEach(t => {
          t.classList.remove('tab-active');
          t.setAttribute('aria-selected', 'false');
        });
        
        // Cache tous les contenus
        tabContents.forEach(content => {
          content.classList.add('hidden');
        });
        
        // Active l'onglet cliqué
        tab.classList.add('tab-active');
        tab.setAttribute('aria-selected', 'true');
        
        // Affiche le contenu correspondant
        const contentId = 'tab-content-' + tab.id.split('-')[1];
        document.getElementById(contentId).classList.remove('hidden');
        
        // Stocke l'onglet actif dans le localStorage
        localStorage.setItem('profileActiveTab', tab.id.split('-')[1]);
      });
    });
    
    // Restaure l'onglet actif depuis localStorage ou utilise l'onglet par défaut
    const savedTab = localStorage.getItem('profileActiveTab');
    if (savedTab) {
      const tabToActivate = document.getElementById('tab-' + savedTab);
      if (tabToActivate) {
        tabToActivate.click();
      }
    }
    
    // Modal d'édition de profil
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const editProfileModal = document.getElementById('edit-profile-modal');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const saveProfileBtn = document.getElementById('save-profile-btn');
    
    if (editProfileBtn && editProfileModal) {
      editProfileBtn.addEventListener('click', () => {
        editProfileModal.showModal();
      });
    }
    
    if (cancelEditBtn) {
      cancelEditBtn.addEventListener('click', () => {
        editProfileModal.close();
      });
    }
    
    if (saveProfileBtn) {
      saveProfileBtn.addEventListener('click', () => {
        // Ici, on pourrait ajouter la logique pour envoyer les données du formulaire
        // via une requête AJAX ou un formulaire classique
        
        // Exemple simplifié pour la démonstration
        const formData = {
          firstname: document.getElementById('edit-firstname').value,
          lastname: document.getElementById('edit-lastname').value,
          pseudo: document.getElementById('edit-pseudo').value,
          username: document.getElementById('edit-username').value,
          email: document.getElementById('edit-email').value,
          discord: document.getElementById('edit-discord').value,
          birthdate: document.getElementById('edit-birthdate').value,
          gender: document.getElementById('edit-gender').value
        };
        
        console.log('Données à envoyer:', formData);
        
        // Mise à jour simulée des données affichées (à remplacer par du code réel)
        document.getElementById('profile-full-name').textContent = formData.firstname + ' ' + formData.lastname;
        document.getElementById('profile-pseudo').textContent = '@' + formData.pseudo;
        document.getElementById('profile-email').textContent = formData.email;
        document.getElementById('profile-username').textContent = 'login: ' + formData.username;
        document.getElementById('profile-discord').textContent = 'Discord: ' + formData.discord;
        
        // Fermeture du modal
        editProfileModal.close();
      });
    }
    
    // Filtres dans l'onglet Historique
    const filterType = document.getElementById('filter-type');
    const filterRole = document.getElementById('filter-role');
    
    if (filterType && filterRole) {
      const updateFilters = () => {
        const typeValue = filterType.value;
        const roleValue = filterRole.value;
        
        const rows = document.querySelectorAll('#tab-content-historique tbody tr');
        
        rows.forEach(row => {
          const rowType = row.getAttribute('data-type');
          const rowRole = row.getAttribute('data-role');
          let showRow = true;
          
          if (typeValue !== 'all' && typeValue !== null && rowType !== typeValue) {
            showRow = false;
          }
          
          if (roleValue !== 'all' && roleValue !== null && rowRole !== roleValue) {
            showRow = false;
          }
          
          row.style.display = showRow ? '' : 'none';
        });
      };
      
      filterType.addEventListener('change', updateFilters);
      filterRole.addEventListener('change', updateFilters);
    }
    
    // Copier le lien iCalendar
    const copyIcsUrlBtn = document.getElementById('copy-ics-url');
    const icsUrlInput = document.getElementById('ics-url');
    
    if (copyIcsUrlBtn && icsUrlInput) {
      copyIcsUrlBtn.addEventListener('click', () => {
        icsUrlInput.select();
        document.execCommand('copy');
        // Notification de copie réussie (à adapter selon l'UI)
        alert('Lien copié dans le presse-papier !');
      });
    }
    
    // Gestion des préférences
    const toggles = document.querySelectorAll('[data-pref]');
    
    toggles.forEach(toggle => {
      toggle.addEventListener('change', () => {
        const preference = toggle.getAttribute('data-pref');
        const value = toggle.checked;
        
        // Ici, on pourrait envoyer la modification de préférence au serveur
        console.log('Préférence modifiée:', preference, value);
        
        // Exemple de notification de changement (à adapter selon l'UI)
        // toast('Préférence mise à jour');
      });
    });
  });
</script>
