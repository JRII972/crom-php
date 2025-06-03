{{-- Script JavaScript pour la gestion des onglets et autres fonctionnalités --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabDescription = document.getElementById('tab-description');
    const tabSessions = document.getElementById('tab-sessions');
    const contentDescription = document.getElementById('tab-content-description');
    const contentSessions = document.getElementById('tab-content-sessions');
    
    if(tabDescription && tabSessions && contentDescription && contentSessions) {
      tabDescription.addEventListener('click', function() {
        // Mise à jour des classes des onglets
        tabDescription.classList.add('tab-active');
        tabSessions.classList.remove('tab-active');
        
        // Affichage du contenu approprié
        contentDescription.classList.remove('hidden');
        contentSessions.classList.add('hidden');
      });
      
      tabSessions.addEventListener('click', function() {
        // Mise à jour des classes des onglets
        tabSessions.classList.add('tab-active');
        tabDescription.classList.remove('tab-active');
        
        // Affichage du contenu approprié
        contentSessions.classList.remove('hidden');
        contentDescription.classList.add('hidden');
      });
    }
    
    // Gestion du bouton d'inscription
    const inscriptionBtn = document.getElementById('inscription-partie-btn');
    if(inscriptionBtn) {
      inscriptionBtn.addEventListener('click', function() {
        alert('Inscription à la partie en cours...');
        // Ici, vous pourriez ajouter un appel AJAX pour l'inscription
      });
    }
    
    // Gestion des boutons d'inscription aux sessions
    const sessionButtons = document.querySelectorAll('.card-body .btn-primary.btn-sm');
    sessionButtons.forEach(button => {
      button.addEventListener('click', function() {
        const sessionTitle = this.closest('.card-body').querySelector('h4.font-bold').textContent;
        alert(`Inscription à la session "${sessionTitle}" en cours...`);
        // Ici, vous pourriez ajouter un appel AJAX pour l'inscription à la session
      });
    });
  });
</script>
