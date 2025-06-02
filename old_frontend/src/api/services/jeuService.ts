import axiosInstance from '../axiosInstance';
import type { Jeu } from '../types/db';

/**
 * Service for handling game-related API calls, typé avec les interfaces.
 */
const jeuService = {
  /**
   * Retrieves a game by its ID.
   */
  getJeu: async (id: number): Promise<Jeu> => {
    const response = await axiosInstance.get(`/jeux/${id}`);
    return response.data.data as Jeu;
  },

  /**
   * Lists games with optional filters.
   */
  listJeux: async (filters = {}): Promise<Jeu[]> => {
    const response = await axiosInstance.get('/jeux', { params: filters });
    return response.data.data as Jeu[];
  },

  /**
   * Creates a new game (admin only).
   */
  createJeu: async (jeuData: Partial<Jeu>): Promise<Jeu> => {
    const response = await axiosInstance.post('/jeux', jeuData);
    return response.data.data as Jeu;
  },

  /**
   * Updates a game by ID (admin only).
   */
  updateJeu: async (id: number, jeuData: Partial<Jeu>): Promise<Jeu> => {
    const response = await axiosInstance.put(`/jeux/${id}`, jeuData);
    return response.data.data as Jeu;
  },

  /**
   * Partially updates a game by ID (admin only).
   */
  patchJeu: async (id: number, jeuData: Partial<Jeu>): Promise<Jeu> => {
    const response = await axiosInstance.patch(`/jeux/${id}`, jeuData);
    return response.data.data as Jeu;
  },

  /**
   * Deletes a game by ID (admin only).
   */
  deleteJeu: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/jeux/${id}`);
    return response.data.data as null;
  },
};

export default jeuService;