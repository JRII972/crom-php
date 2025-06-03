/**
 * Scripts spécifiques à la page de contact
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialiser le formulaire de contact
  initContactForm();
  
  // Initialiser l'accordéon FAQ
  initFaqAccordion();
});

/**
 * Initialise le formulaire de contact
 */
function initContactForm() {
  const contactForm = document.querySelector('.card-body form');
  if (!contactForm) return;
  
  contactForm.addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Validation du formulaire
    const requiredFields = contactForm.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        isValid = false;
        field.classList.add('input-error');
      } else {
        field.classList.remove('input-error');
      }
    });
    
    // Traitement du formulaire si valide
    if (isValid) {
      // Simulation d'envoi du formulaire
      alert('Votre message a été envoyé avec succès !');
      contactForm.reset();
    } else {
      alert('Veuillez remplir tous les champs obligatoires.');
    }
  });
}

/**
 * Initialise l'accordéon de la FAQ
 */
function initFaqAccordion() {
  const faqItems = document.querySelectorAll('.collapse input[name="faq-accordion"]');
  if (faqItems.length === 0) return;
  
  faqItems.forEach(item => {
    item.addEventListener('change', function() {
      if (this.checked) {
        // Fermer tous les autres accordéons
        faqItems.forEach(otherItem => {
          if (otherItem !== this) {
            otherItem.checked = false;
          }
        });
      }
    });
  });
}