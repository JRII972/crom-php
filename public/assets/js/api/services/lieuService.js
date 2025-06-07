import axiosInstance from '../axiosInstance';

/**
 * Service for handling location-related API calls.
 */
const lieuService = {
  /**
   * Retrieves a location by its ID.
   */
  getLieu: async (id) => {
    const response = await axiosInstance.get(`/lieux/${id}`);
    return response.data.data;
  },

  /**
   * Lists locations with optional filters.
   */
  listLieux: async (filters = {}) => {
    const response = await axiosInstance.get('/lieux', { params: filters });
    return response.data.data;
  },

  /**
   * Creates a new location (admin only).
   */
  createLieu: async (lieuData) => {
    const response = await axiosInstance.post('/lieux', lieuData);
    return response.data.data;
  },

  /**
   * Updates a location by ID (admin only).
   */
  updateLieu: async (id, lieuData) => {
    const response = await axiosInstance.put(`/lieux/${id}`, lieuData);
    return response.data.data;
  },

  /**
   * Deletes a location by ID (admin only).
   */
  deleteLieu: async (id) => {
    const response = await axiosInstance.delete(`/lieux/${id}`);
    return response.data.data;
  },

  /**
   * Retrieves schedules for a location.
   */
  getHoraires: async (id) => {
    const response = await axiosInstance.get(`/lieux/${id}/horaires`);
    return response.data.data;
  },

  /**
   * Adds a new schedule to a location (admin only).
   */
  addHoraire: async (id, horaireData) => {
    const response = await axiosInstance.post(`/lieux/${id}/horaires`, horaireData);
    return response.data.data;
  },

  /**
   * Updates a schedule for a location (admin only).
   */
  updateHoraire: async (id, horaireData) => {
    const response = await axiosInstance.patch(`/lieux/${id}/horaires`, horaireData);
    return response.data.data;
  },

  /**
   * Deletes a schedule for a location (admin only).
   */
  deleteHoraire: async (id, horaireData) => {
    const response = await axiosInstance.delete(`/lieux/${id}/horaires`, { data: horaireData });
    return response.data.data;
  },
};

export default lieuService;
