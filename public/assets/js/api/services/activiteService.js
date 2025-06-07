import axiosInstance from '../axiosInstance';

/**
 * Service for handling party-related API calls.
 */
const activiteService = {
  /**
   * Retrieves a party by its ID.
   */
  getActivite: async (id) => {
    const response = await axiosInstance.get(`/activites/${id}`);
    return response.data.data;
  },

  /**
   * Lists activites with optional filters.
   */
  listActivites: async (filters = {}) => {
    const response = await axiosInstance.get('/activites', { params: filters });
    return response.data.data;
  },

  /**
   * Creates a new party (game master or admin only).
   */
  createActivite: async (activiteData) => {
    const response = await axiosInstance.post('/activites', activiteData);
    return response.data.data;
  },

  /**
   * Updates a party by ID (game master or admin only).
   */
  updateActivite: async (id, activiteData) => {
    const response = await axiosInstance.put(`/activites/${id}`, activiteData);
    return response.data.data;
  },

  /**
   * Partially updates a party by ID (game master or admin only).
   */
  patchActivite: async (id, activiteData) => {
    const response = await axiosInstance.patch(`/activites/${id}`, activiteData);
    return response.data.data;
  },

  /**
   * Deletes a party by ID (game master or admin only).
   */
  deleteActivite: async (id) => {
    const response = await axiosInstance.delete(`/activites/${id}`);
    return response.data.data;
  },

  /**
   * Adds a member to a party (game master, admin, or self).
   */
  addMember: async (id, memberData) => {
    const response = await axiosInstance.post(`/activites/${id}/membres`, memberData);
    return response.data.data;
  },

  /**
   * Removes a member from a party (game master, admin, or self).
   */
  removeMember: async (id, userId) => {
    const response = await axiosInstance.delete(`/activites/${id}/membres/${userId}`);
    return response.data.data;
  },
};

export default activiteService;
