import axiosInstance from '../axiosInstance';

/**
 * Service for handling game-related API calls.
 */
const jeuService = {
  /**
   * Retrieves a game by its ID.
   */
  getJeu: async (id) => {
    const response = await axiosInstance.get(`/jeux/${id}`);
    return response.data.data;
  },

  /**
   * Lists games with optional filters.
   */
  listJeux: async (filters = {}) => {
    const response = await axiosInstance.get('/jeux', { params: filters });
    return response.data.data;
  },

  /**
   * Creates a new game (admin only).
   */
  createJeu: async (jeuData) => {
    const response = await axiosInstance.post('/jeux', jeuData);
    return response.data.data;
  },

  /**
   * Updates a game by ID (admin only).
   */
  updateJeu: async (id, jeuData) => {
    const response = await axiosInstance.put(`/jeux/${id}`, jeuData);
    return response.data.data;
  },

  /**
   * Partially updates a game by ID (admin only).
   */
  patchJeu: async (id, jeuData) => {
    const response = await axiosInstance.patch(`/jeux/${id}`, jeuData);
    return response.data.data;
  },

  /**
   * Deletes a game by ID (admin only).
   */
  deleteJeu: async (id) => {
    const response = await axiosInstance.delete(`/jeux/${id}`);
    return response.data.data;
  },
};

export default jeuService;
