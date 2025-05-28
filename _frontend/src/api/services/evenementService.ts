import axiosInstance from '../axiosInstance';
import type { Evenement } from '../types/db';

/**
 * Service for handling event-related API calls, typé avec les interfaces.
 */
const evenementService = {
  /**
   * Retrieves an event by its ID.
   */
  getEvenement: async (id: number): Promise<Evenement> => {
    const response = await axiosInstance.get(`/evenements/${id}`);
    return response.data.data as Evenement;
  },

  /**
   * Lists events with optional date filter.
   */
  listEvenements: async (filters = {}): Promise<Evenement[]> => {
    const response = await axiosInstance.get('/evenements', { params: filters });
    return response.data.data as Evenement[];
  },

  /**
   * Creates a new event (admin only).
   */
  createEvenement: async (evenementData: Partial<Evenement>): Promise<Evenement> => {
    const response = await axiosInstance.post('/evenements', evenementData);
    return response.data.data as Evenement;
  },

  /**
   * Updates an event by ID (admin only).
   */
  updateEvenement: async (id: number, evenementData: Partial<Evenement>): Promise<Evenement> => {
    const response = await axiosInstance.put(`/evenements/${id}`, evenementData);
    return response.data.data as Evenement;
  },

  /**
   * Deletes an event by ID (admin only).
   */
  deleteEvenement: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/evenements/${id}`);
    return response.data.data as null;
  },
};

export default evenementService;