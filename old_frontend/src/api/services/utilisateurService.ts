import axiosInstance from '../axiosInstance';
import type { Utilisateur, CreneauUtilisateur } from '../types/db';

const utilisateurService = {
  login: async (
    login: string,
    mot_de_passe: string,
    keep_logged_in = false
  ): Promise<{ token: string; refresh_token: string; utilisateur: Utilisateur }> => {
    const response = await axiosInstance.post('/utilisateurs/connexion', {
      login,
      mot_de_passe,
      keep_logged_in,
    });
    return response.data.data;
  },

  inscription: async (
    userData: Partial<Utilisateur> & { mot_de_passe: string }
  ): Promise<{ token: string; utilisateur: Utilisateur }> => {
    const response = await axiosInstance.post('/utilisateurs/inscription', userData);
    return response.data.data;
  },

  refreshToken: async (
    refreshToken: string
  ): Promise<{ token: string; utilisateur: Utilisateur }> => {
    const response = await axiosInstance.post('/utilisateurs/refresh', {
      refresh_token: refreshToken,
    });
    return response.data.data;
  },

  getUtilisateur: async (id: string): Promise<Utilisateur> => {
    const response = await axiosInstance.get(`/utilisateurs/${id}`);
    return response.data.data;
  },

  updateUtilisateur: async (
    id: string,
    userData: Partial<Utilisateur>
  ): Promise<Utilisateur> => {
    const response = await axiosInstance.put(`/utilisateurs/${id}`, userData);
    return response.data.data;
  },

  deleteUtilisateur: async (id: string): Promise<null> => {
    const response = await axiosInstance.delete(`/utilisateurs/${id}`);
    return response.data.data;
  },

  getCreneaux: async (userId: string): Promise<CreneauUtilisateur[]> => {
    const response = await axiosInstance.get(`/utilisateurs/${userId}/creneaux`);
    return response.data.data;
  },

  postCreneau: async (
    userId: string,
    creneauData: Partial<CreneauUtilisateur>
  ): Promise<CreneauUtilisateur> => {
    const response = await axiosInstance.post(`/utilisateurs/${userId}/creneaux`, creneauData);
    return response.data.data;
  },

  updateCreneau: async (
    userId: string,
    creneauData: Partial<CreneauUtilisateur>
  ): Promise<CreneauUtilisateur> => {
    const response = await axiosInstance.patch(`/utilisateurs/${userId}/creneaux`, creneauData);
    return response.data.data;
  },

  deleteCreneau: async (userId: string, creneauId: number): Promise<null> => {
    const response = await axiosInstance.delete(`/utilisateurs/${userId}/creneaux/${creneauId}`);
    return response.data.data;
  },
};

export default utilisateurService;