// Service d'authentification utilisant les cookies JWT
import axiosInstance from '../axiosInstance.js';

/**
 * Service d'authentification
 */
class AuthService {
  /**
   * Connecter un utilisateur
   * @param {string} login - Login de l'utilisateur
   * @param {string} password - Mot de passe
   * @param {boolean} keepLoggedIn - Rester connecté
   * @returns {Promise<Object>} Données de l'utilisateur
   */
  async login(login, password, keepLoggedIn = false) {
    try {
      const response = await axiosInstance.post('/utilisateurs/connexion', {
        login,
        mot_de_passe: password,
        keep_logged_in: keepLoggedIn
      });

      if (response.data.status === 'success') {
        // Le cookie est automatiquement défini par le serveur
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Erreur de connexion');
      }
    } catch (error) {
      console.error('Erreur lors de la connexion:', error);
      throw error.response?.data?.message || error.message || 'Erreur de connexion';
    }
  }

  /**
   * Inscrire un nouvel utilisateur
   * @param {Object} userData - Données de l'utilisateur
   * @returns {Promise<Object>} Données de l'utilisateur
   */
  async register(userData) {
    try {
      const response = await axiosInstance.post('/utilisateurs/inscription', userData);

      if (response.data.status === 'success') {
        // Le cookie est automatiquement défini par le serveur
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Erreur d\'inscription');
      }
    } catch (error) {
      console.error('Erreur lors de l\'inscription:', error);
      throw error.response?.data?.message || error.message || 'Erreur d\'inscription';
    }
  }

  /**
   * Déconnecter l'utilisateur
   * @param {string} refreshToken - Token de rafraîchissement (optionnel)
   * @returns {Promise<void>}
   */
  async logout(refreshToken = null) {
    try {
      const data = refreshToken ? { refresh_token: refreshToken } : {};
      await axiosInstance.post('/utilisateurs/deconnexion', data);
      
      // Supprimer les tokens du localStorage
      localStorage.removeItem('token');
      localStorage.removeItem('refreshToken');
      
      // Le cookie sera supprimé automatiquement par le serveur
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error);
      // Même en cas d'erreur, on supprime les tokens locaux
      localStorage.removeItem('token');
      localStorage.removeItem('refreshToken');
    }
  }

  /**
   * Récupérer les informations de l'utilisateur connecté
   * @returns {Promise<Object>} Données de l'utilisateur
   */
  async getCurrentUser() {
    try {
      const response = await axiosInstance.get('/utilisateurs/me');
      
      if (response.data.status === 'success') {
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Utilisateur non trouvé');
      }
    } catch (error) {
      console.error('Erreur lors de la récupération de l\'utilisateur:', error);
      throw error.response?.data?.message || error.message || 'Erreur de récupération';
    }
  }

  /**
   * Rafraîchir le token d'authentification
   * @param {string} refreshToken - Token de rafraîchissement
   * @returns {Promise<Object>} Nouvelles données d'authentification
   */
  async refreshToken(refreshToken) {
    try {
      const response = await axiosInstance.post('/utilisateurs/refresh', {
        refresh_token: refreshToken
      });

      if (response.data.status === 'success') {
        const { token } = response.data.data;
        localStorage.setItem('token', token);
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Erreur de rafraîchissement');
      }
    } catch (error) {
      console.error('Erreur lors du rafraîchissement:', error);
      throw error.response?.data?.message || error.message || 'Erreur de rafraîchissement';
    }
  }

  /**
   * Vérifier si l'utilisateur est connecté
   * @returns {Promise<boolean>} True si connecté
   */
  async isAuthenticated() {
    try {
      await this.getCurrentUser();
      return true;
    } catch (error) {
      return false;
    }
  }

  /**
   * Vérifier la connexion sans déclencher de rechargement
   * @returns {Promise<boolean>} True si connecté
   */
  async checkConnection() {
    try {
      const response = await axiosInstance.get('/utilisateurs/reconnect');
      return response.data.status === 'success';
    } catch (error) {
      return false;
    }
  }
}

// Instance singleton du service d'authentification
const authService = new AuthService();

export default authService;
