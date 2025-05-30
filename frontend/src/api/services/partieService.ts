import axiosInstance from '../axiosInstance';
import type { Partie, MembrePartie } from '../types/db';

/**
 * Service for handling party-related API calls, typé avec les interfaces.
 */
const partieService = {
  /**
   * Retrieves a party by its ID.
   */
  getPartie: async (id: number): Promise<Partie> => {
    const response = await axiosInstance.get(`/parties/${id}`);
    return response.data.data as Partie;
  },

  /**
   * Lists parties with optional filters.
   */
  listParties: async (filters = {}): Promise<Partie[]> => {
    const response = await axiosInstance.get('/parties', { params: filters });
    return response.data.data as Partie[];
  },

  /**
   * Creates a new party (game master or admin only).
   */
  createPartie: async (partieData: Partial<Partie>): Promise<Partie> => {
    const response = await axiosInstance.post('/parties', partieData);
    return response.data.data as Partie;
  },

  /**
   * Updates a party by ID (game master or admin only).
   */
  updatePartie: async (id: number, partieData: Partial<Partie>): Promise<Partie> => {
    const response = await axiosInstance.put(`/parties/${id}`, partieData);
    return response.data.data as Partie;
  },

  /**
   * Partially updates a party by ID (game master or admin only).
   */
  patchPartie: async (id: number, partieData: Partial<Partie>): Promise<Partie> => {
    const response = await axiosInstance.patch(`/parties/${id}`, partieData);
    return response.data.data as Partie;
  },

  /**
   * Deletes a party by ID (game master or admin only).
   */
  deletePartie: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/parties/${id}`);
    return response.data.data as null;
  },

  /**
   * Adds a member to a party (game master, admin, or self).
   */
  addMember: async (id: number, memberData: { id_utilisateur: string }): Promise<MembrePartie> => {
    const response = await axiosInstance.post(`/parties/${id}/membres`, memberData);
    return response.data.data as MembrePartie;
  },

  /**
   * Removes a member from a party (game master, admin, or self).
   */
  removeMember: async (id: number, userId: string): Promise<null> => {
    const response = await axiosInstance.delete(`/parties/${id}/membres/${userId}`);
    return response.data.data as null;
  },
};

export default partieService;