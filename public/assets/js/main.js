import utilisateurService from "./api/services/utilisateurService";

/**
 * Scripts généraux pour toutes les pages
 */

if (localStorage.getItem('theme-mode')){
  document.documentElement.setAttribute('data-theme',localStorage.getItem('theme-mode'))
}

document.addEventListener('DOMContentLoaded', function() {
  // Formater la date au format "29 Juil 2025"
  const dateElement = document.getElementById('current-date');
  if (dateElement) {
    dateElement.textContent = new Date().toLocaleDateString('fr-FR', {
      day: 'numeric', 
      month: 'short', 
      year: 'numeric'
    });
  }
  
  // Fonction pour changer le thème
  document.querySelectorAll('[data-set-theme]').forEach(button => {
    button.addEventListener('click', () => {
      document.documentElement.setAttribute('data-theme', button.getAttribute('data-set-theme'));
      localStorage.setItem('theme-mode', button.getAttribute('data-set-theme'));
    });
  });

  // Gestion de la navbar qui disparaît au défilement
  initNavbarScroll();
});

/**
 * Initialise le comportement de la navbar au défilement
 */
function initNavbarScroll() {
  let lastScrollTop = 0;
  const minTop = 20; // Seuil où le menu doit toujours être affiché
  let scrollThreshold = 30; // Seuil pour considérer un défilement "fort"
  let scrollTimer = null;
  const navbar = document.querySelector('.navbar');
  const scrollContainer = document.querySelector('.drawer-content');
  
  if (!navbar || !scrollContainer) return;
  
  // Ajout des classes de transition sur la navbar
  navbar.classList.add('transition-transform', 'duration-300', 'ease-in-out');
  
  // Écoute l'événement de défilement sur le conteneur qui défile réellement
  scrollContainer.addEventListener('scroll', function() {
    // Effacer le timeout précédent
    clearTimeout(scrollTimer);
    
    // Position actuelle du défilement
    const st = scrollContainer.scrollTop;
    
    if (st < minTop) {            
      navbar.classList.remove('-translate-y-full');
    }

    // Calcul de la différence de défilement
    const scrollDifference = Math.abs(st - lastScrollTop);
    
    // Vérifier si le défilement est assez "fort" (rapide)
    if (scrollDifference > scrollThreshold) {
      // Défilement vers le bas = masquer la navbar
      if (st > lastScrollTop && st > navbar.offsetHeight) {
        navbar.classList.add('-translate-y-full');
      } 
      // Défilement vers le haut = afficher la navbar
      else if (st < lastScrollTop) {
        navbar.classList.remove('-translate-y-full');
      }
    }
    
    // Mettre à jour la dernière position de défilement après un court délai
    scrollTimer = setTimeout(function() {
      lastScrollTop = st <= 0 ? 0 : st;
    }, 50);
  }, { passive: true });
}

try {
  utilisateurService.reconnect();
  console.log('Reconnect succes');
} catch {
  console.log('Reconnect faild');
}