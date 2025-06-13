// Utilitaires pour la gestion des cookies
export const CookieUtils = {
  /**
   * Récupérer la valeur d'un cookie
   * @param {string} name - Nom du cookie
   * @returns {string|null} Valeur du cookie ou null si non trouvé
   */
  get(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
      return parts.pop().split(';').shift();
    }
    return null;
  },

  /**
   * Définir un cookie
   * @param {string} name - Nom du cookie
   * @param {string} value - Valeur du cookie
   * @param {Object} options - Options du cookie
   * @param {number} options.days - Nombre de jours avant expiration
   * @param {string} options.path - Chemin du cookie
   * @param {string} options.domain - Domaine du cookie
   * @param {boolean} options.secure - Cookie sécurisé (HTTPS uniquement)
   * @param {boolean} options.httpOnly - Cookie accessible uniquement via HTTP (non supporté côté client)
   * @param {string} options.sameSite - Politique SameSite
   */
  set(name, value, options = {}) {
    const {
      days = 7,
      path = '/',
      domain = '',
      secure = location.protocol === 'https:',
      sameSite = 'Lax'
    } = options;

    let cookieString = `${name}=${value}`;
    
    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      cookieString += `; expires=${date.toUTCString()}`;
    }
    
    if (path) {
      cookieString += `; path=${path}`;
    }
    
    if (domain) {
      cookieString += `; domain=${domain}`;
    }
    
    if (secure) {
      cookieString += `; secure`;
    }
    
    if (sameSite) {
      cookieString += `; samesite=${sameSite}`;
    }

    document.cookie = cookieString;
  },

  /**
   * Supprimer un cookie
   * @param {string} name - Nom du cookie
   * @param {string} path - Chemin du cookie
   * @param {string} domain - Domaine du cookie
   */
  remove(name, path = '/', domain = '') {
    let cookieString = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC`;
    
    if (path) {
      cookieString += `; path=${path}`;
    }
    
    if (domain) {
      cookieString += `; domain=${domain}`;
    }

    document.cookie = cookieString;
  },

  /**
   * Vérifier si un cookie existe
   * @param {string} name - Nom du cookie
   * @returns {boolean} True si le cookie existe
   */
  exists(name) {
    return this.get(name) !== null;
  },

  /**
   * Récupérer tous les cookies sous forme d'objet
   * @returns {Object} Objet contenant tous les cookies
   */
  getAll() {
    const cookies = {};
    document.cookie.split(';').forEach(cookie => {
      const [name, value] = cookie.trim().split('=');
      if (name && value) {
        cookies[name] = decodeURIComponent(value);
      }
    });
    return cookies;
  },

  /**
   * Vérifier si l'utilisateur a un token d'authentification
   * @returns {boolean} True si un token d'auth est présent
   */
  hasAuthToken() {
    return this.exists('auth_token');
  },

  /**
   * Récupérer le token d'authentification
   * @returns {string|null} Token d'authentification ou null
   */
  getAuthToken() {
    return this.get('auth_token');
  }
};

export default CookieUtils;
