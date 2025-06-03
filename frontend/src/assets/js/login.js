/**
 * Gestion du formulaire de connexion
 */

/**
 * Appel API pour la connexion utilisateur (retourne une promesse)
 */
function apiLogin(login, motDePasse, keepLoggedIn) {
  return fetch('  /api/utilisateurs/connexion', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      login: login,
      mot_de_passe: motDePasse,
      keep_logged_in: keepLoggedIn
    })
  }).then(async res => {
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Erreur lors de la connexion.');
    return data;
  });
}

/**
 * Appel API pour rafraîchir le token (retourne une promesse)
 */
function apiRefreshToken(refreshToken) {
  return fetch('  /api/utilisateurs/refresh', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ refresh_token: refreshToken })
  }).then(async res => {
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Session expirée.');
    return data;
  });
}

function getRedirectUrl() {
  const params = new URLSearchParams(window.location.search);
  return params.get('redirect') || '/';
}

const checkLoggedIn = async () => {
  const token = localStorage.getItem('token');
  const refreshToken = localStorage.getItem('refreshToken');

  if (!token && !refreshToken) {
    return;
  }

  try {
    showLoading(true);
    // Si refreshToken existe, tente de rafraîchir le token
    if (refreshToken) {
      const data = await apiRefreshToken(refreshToken);
      if (data.token) {
        localStorage.setItem('token', data.token);
        if (data.refresh_token) {
          localStorage.setItem('refreshToken', data.refresh_token);
        }
        showLoading(false);
        window.location.href = getRedirectUrl();
        return;
      }
    }
    // Si pas de refreshToken ou échec, on reste sur la page de login
    showLoading(false);
  } catch (err) {
    showLoading(false);
    showError('Session expirée, veuillez vous reconnecter.');
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
  }
};

/**
 * Initialise les champs du formulaire avec les valeurs stockées si disponibles
 */
function initFormFields() {
  // Vérifier si "Se souvenir de moi" était coché
  const rememberedEmail = localStorage.getItem('rememberedEmail');
  const rememberMe = document.getElementById('remember-me');
  
  if (rememberedEmail) {
    document.getElementById('email').value = rememberedEmail;
    rememberMe.checked = true;
  }
}

/**
 * Gère la soumission du formulaire de connexion
 * @param {Event} event - L'événement de soumission
 */
function handleLoginSubmit(event) {
  event.preventDefault();

  // Récupération des valeurs du formulaire
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const rememberMe = document.getElementById('remember-me').checked;

  showLoading(true);
  apiLogin(email, password, rememberMe)
    .then(function(data) {
      if (data.status == 'success') {
        localStorage.setItem('token', data.token);
        if (rememberMe && data.refresh_token) {
          localStorage.setItem('refreshToken', data.refresh_token);
        } else {
          localStorage.removeItem('refreshToken');
        }
        window.location.href = getRedirectUrl();
      } else {
        showError('Connexion impossible.');
      }
    })
    .catch(function(err) {
      showError(err.message);
    })
    .finally(function() {
      showLoading(false);
    });
}

/**
 * Affiche un message d'erreur
 * @param {string} message - Le message d'erreur à afficher
 */
function showError(message) {
  // Cette fonction pourrait utiliser une bibliothèque de toasts ou créer une alerte personnalisée
  alert(message);
}

// Fonction de déconnexion
const logout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('refreshToken');
};

/**
 * Affiche ou masque l'indicateur de chargement
 * @param {boolean} show - Indique si l'indicateur doit être affiché
 */
function showLoading(show) {
  const submitButton = document.querySelector('#login-form button[type="submit"]');
  
  if (submitButton) {
    if (show) {
      // Désactiver le bouton et changer le texte
      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Connexion en cours...';
    } else {
      // Réactiver le bouton et restaurer le texte
      submitButton.disabled = false;
      submitButton.textContent = 'Se connecter';
    }
  }
}


checkLoggedIn();


document.addEventListener('DOMContentLoaded', function() {
  // Récupération du formulaire
  const loginForm = document.getElementById('login-form');
  
  // Vérification si le formulaire existe sur la page
  if (loginForm) {
    // Ajout de l'événement de soumission du formulaire
    loginForm.addEventListener('submit', handleLoginSubmit);
    
    // Initialisation des champs si des valeurs sont stockées
    initFormFields();
  }
  
  // Mise à jour de la date courante
  const dateElement = document.getElementById('current-date');
  if (dateElement) {
    dateElement.textContent = new Date().toLocaleDateString('fr-FR', {
      day: 'numeric', 
      month: 'short', 
      year: 'numeric'
    });
  }
});