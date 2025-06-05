import axiosInstance from '../axiosInstance';
import type { Activite, MembreActivite } from '../types/db';

/**
 * Service for handling party-related API calls, typé avec les interfaces.
 */
const activiteService = {
  /**
   * Retrieves a party by its ID.
   */
  getActivite: async (id: number): Promise<Activite> => {
    const response = await axiosInstance.get(`/activites/${id}`);
    return response.data.data as Activite;
  },

  /**
   * Lists activites with optional filters.
   */
  listActivites: async (filters = {}): Promise<Activite[]> => {
    const response = await axiosInstance.get('/activites', { params: filters });
    return response.data.data as Activite[];
  },

  /**
   * Creates a new party (game master or admin only).
   */
  createActivite: async (activiteData: Partial<Activite>): Promise<Activite> => {
    const response = await axiosInstance.post('/activites', activiteData);
    return response.data.data as Activite;
  },

  /**
   * Updates a party by ID (game master or admin only).
   */
  updateActivite: async (id: number, activiteData: Partial<Activite>): Promise<Activite> => {
    const response = await axiosInstance.put(`/activites/${id}`, activiteData);
    return response.data.data as Activite;
  },

  /**
   * Partially updates a party by ID (game master or admin only).
   */
  patchActivite: async (id: number, activiteData: Partial<Activite>): Promise<Activite> => {
    const response = await axiosInstance.patch(`/activites/${id}`, activiteData);
    return response.data.data as Activite;
  },

  /**
   * Deletes a party by ID (game master or admin only).
   */
  deleteActivite: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/activites/${id}`);
    return response.data.data as null;
  },

  /**
   * Adds a member to a party (game master, admin, or self).
   */
  addMember: async (id: number, memberData: { id_utilisateur: string }): Promise<MembreActivite> => {
    const response = await axiosInstance.post(`/activites/${id}/membres`, memberData);
    return response.data.data as MembreActivite;
  },

  /**
   * Removes a member from a party (game master, admin, or self).
   */
  removeMember: async (id: number, userId: string): Promise<null> => {
    const response = await axiosInstance.delete(`/activites/${id}/membres/${userId}`);
    return response.data.data as null;
  },
};

export default activiteService;