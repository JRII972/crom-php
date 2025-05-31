import React, { createContext, useContext, useState, useEffect } from 'react';
import utilisateurService from '../api/services/utilisateurService';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Vérifier si l'utilisateur est connecté au chargement de l'application
  useEffect(() => {
    const checkLoggedIn = async () => {
      const token = localStorage.getItem('token');
      const refreshToken = localStorage.getItem('refreshToken');

      if (!token) {
        setLoading(false);
        return;
      }

      try {
        // Si le token existe, essayez de récupérer les données utilisateur
        // Vous pouvez implémenter une vérification de token ici
        
        // Si le token est expiré et qu'un refreshToken existe, rafraîchissez le token
        if (refreshToken) {
          const { token: newToken, utilisateur } = await utilisateurService.refreshToken(refreshToken);
          localStorage.setItem('token', newToken);
          setUser(utilisateur);
        }
      } catch (err) {
        console.error('Erreur d\'authentification:', err);
        localStorage.removeItem('token');
        localStorage.removeItem('refreshToken');
      } finally {
        setLoading(false);
      }
    };

    checkLoggedIn();
  }, []);

  // Fonction de connexion
  const login = async (login, motDePasse, keepLoggedIn) => {
    try {
      const { token, refresh_token, utilisateur } = await utilisateurService.login(login, motDePasse, keepLoggedIn);
      localStorage.setItem('token', token);
      if (keepLoggedIn && refresh_token) {
        localStorage.setItem('refreshToken', refresh_token);
      }
      setUser(utilisateur);
      return utilisateur;
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  // Fonction de déconnexion
  const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    setUser(null);
  };

  // Mise à jour des informations utilisateur
  const updateUserInfo = async (userData) => {
    try {
      if (!user || !user.id) return;
      
      const updatedUser = await utilisateurService.updateUtilisateur(user.id, userData);
      setUser(updatedUser);
      return updatedUser;
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const value = {
    user,
    loading,
    error,
    login,
    logout,
    updateUserInfo,
    isAuthenticated: !!user,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

// Hook personnalisé pour utiliser le contexte d'authentification
export const useAuth = () => {
  const context = useContext(AuthContext);
  
  if (!context) {
    throw new Error('useAuth doit être utilisé à l\'intérieur d\'un AuthProvider');
  }
  
  return context;
};