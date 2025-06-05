{{-- Scripts pour la page activites --}}
<script>
  // Gestion du bouton de filtre par catégorie
  document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('dropdown-activites-btn');
    if (dropdownBtn) {
      const dropdownBtnText = dropdownBtn.querySelector('span');
      const dropdownItems = document.querySelectorAll('#dropdown-activites .dropdown-content a');
      
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
          
          // Supprimer la classe active et selected de tous les éléments
          dropdownItems.forEach(el => {
            el.classList.remove('active', 'selected');
            el.setAttribute('aria-selected', 'false');
          });
          
          // Ajouter la classe active et selected à l'élément cliqué
          this.classList.add('active', 'selected');
          this.setAttribute('aria-selected', 'true');
          
          // Filtrer le contenu
          filterContent(filterValue);
        });
      });
    }
  });

  // Fonction pour filtrer le contenu
  function filterContent(filter) {
    console.log(`Filtrage par: ${filter}`);
    // Logique de filtrage à implémenter
  }

  // Gestion des boutons de vue (liste/grille)
  document.addEventListener('DOMContentLoaded', function() {
    const listView = document.getElementById('liste-view');
    const gridView = document.getElementById('grid-view');
    
    if (listView && gridView) {
      listView.addEventListener('change', function() {
        if (this.checked) {
          document.body.classList.remove('grid-view');
          document.body.classList.add('list-view');
        }
      });
      
      gridView.addEventListener('change', function() {
        if (this.checked) {
          document.body.classList.remove('list-view');
          document.body.classList.add('grid-view');
        }
      });
    }
  });
</script>
