import axiosInstance from '../axiosInstance';

/**
 * Service for handling event-related API calls.
 */
const evenementService = {
  /**
   * Retrieves an event by its ID.
   */
  getEvenement: async (id) => {
    const response = await axiosInstance.get(`/evenements/${id}`);
    return response.data.data;
  },

  /**
   * Lists events with optional date filter.
   */
  listEvenements: async (filters = {}) => {
    const response = await axiosInstance.get('/evenements', { params: filters });
    return response.data.data;
  },

  /**
   * Creates a new event (admin only).
   */
  createEvenement: async (evenementData) => {
    const response = await axiosInstance.post('/evenements', evenementData);
    return response.data.data;
  },

  /**
   * Updates an event by ID (admin only).
   */
  updateEvenement: async (id, evenementData) => {
    const response = await axiosInstance.put(`/evenements/${id}`, evenementData);
    return response.data.data;
  },

  /**
   * Deletes an event by ID (admin only).
   */
  deleteEvenement: async (id) => {
    const response = await axiosInstance.delete(`/evenements/${id}`);
    return response.data.data;
  },
};

export default evenementService;
