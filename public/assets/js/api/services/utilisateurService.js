import axiosInstance from '../axiosInstance';

const utilisateurService = {

  getUtilisateur: async (id) => {
    const response = await axiosInstance.get(`/utilisateurs/${id}`);
    return response.data.data;
  },

  updateUtilisateur: async (id, userData) => {
    const response = await axiosInstance.put(`/utilisateurs/${id}`, userData);
    return response.data.data;
  },

  deleteUtilisateur: async (id) => {
    const response = await axiosInstance.delete(`/utilisateurs/${id}`);
    return response.data.data;
  },

  getCreneaux: async (userId) => {
    const response = await axiosInstance.get(`/utilisateurs/${userId}/creneaux`);
    return response.data.data;
  },

  postCreneau: async (userId, creneauData) => {
    const response = await axiosInstance.post(`/utilisateurs/${userId}/creneaux`, creneauData);
    return response.data.data;
  },

  updateCreneau: async (userId, creneauData) => {
    const response = await axiosInstance.patch(`/utilisateurs/${userId}/creneaux`, creneauData);
    return response.data.data;
  },

  deleteCreneau: async (userId, creneauId) => {
    const response = await axiosInstance.delete(`/utilisateurs/${userId}/creneaux/${creneauId}`);
    return response.data.data;
  },

};

export default utilisateurService;
