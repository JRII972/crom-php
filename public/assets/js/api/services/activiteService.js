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
    return response.data;
  },

  /**
   * Lists activites with optional filters.
   */
  listActivites: async (filters = {}) => {
    const response = await axiosInstance.get('/activites', { params: filters });
    return response.data;
  },

  /**
   * Creates a new party (game master or admin only).
   * @param {Object|FormData} activiteData - The party data (object or FormData for file uploads)
   * @param {boolean} hasFile - Set to true if FormData contains a file upload
   */
  createActivite: async (activiteData, hasFile = false) => {
    const config = {};
    if (hasFile) {
      config.headers = {
        'Content-Type': 'multipart/form-data'
      };
    }
    const response = await axiosInstance.post('/activites', activiteData, config);
    return response.data;
  },

  /**
   * Updates a party by ID (game master or admin only).
   * @param {number|string} id - The party ID
   * @param {Object|FormData} activiteData - The party data (object or FormData for file uploads)
   * @param {boolean} hasFile - Set to true if FormData contains a file upload
   */
  updateActivite: async (id, activiteData, hasFile = false) => {
    const config = {};
    if (hasFile) {
      config.headers = {
        'Content-Type': 'multipart/form-data'
      };
    }
    const response = await axiosInstance.put(`/activites/${id}`, activiteData, config);
    return response.data;
  },

  /**
   * Partially updates a party by ID (game master or admin only).
   */
  patchActivite: async (id, activiteData) => {
    const response = await axiosInstance.patch(`/activites/${id}`, activiteData);
    return response.data;
  },

  /**
   * Deletes a party by ID (game master or admin only).
   */
  deleteActivite: async (id) => {
    const response = await axiosInstance.delete(`/activites/${id}`);
    return response.data;
  },

  /**
   * Adds a member to a party (game master, admin, or self).
   */
  addMember: async (id, memberData) => {
    const response = await axiosInstance.post(`/activites/${id}/membres`, memberData);
    return response.data;
  },

  /**
   * Removes a member from a party (game master, admin, or self).
   */
  removeMember: async (id, userId) => {
    const response = await axiosInstance.delete(`/activites/${id}/membres/${userId}`);
    return response.data;
  },
};

export default activiteService;
