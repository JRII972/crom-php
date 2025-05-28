import axiosInstance from '../axiosInstance';
import type { Lieu, HoraireLieu } from '../types/db';

/**
 * Service for handling location-related API calls, typé avec les interfaces.
 */
const lieuService = {
  /**
   * Retrieves a location by its ID.
   */
  getLieu: async (id: number): Promise<Lieu> => {
    const response = await axiosInstance.get(`/lieux/${id}`);
    return response.data.data as Lieu;
  },

  /**
   * Lists locations with optional filters.
   */
  listLieux: async (filters = {}): Promise<Lieu[]> => {
    const response = await axiosInstance.get('/lieux', { params: filters });
    return response.data.data as Lieu[];
  },

  /**
   * Creates a new location (admin only).
   */
  createLieu: async (lieuData: Partial<Lieu>): Promise<Lieu> => {
    const response = await axiosInstance.post('/lieux', lieuData);
    return response.data.data as Lieu;
  },

  /**
   * Updates a location by ID (admin only).
   */
  updateLieu: async (id: number, lieuData: Partial<Lieu>): Promise<Lieu> => {
    const response = await axiosInstance.put(`/lieux/${id}`, lieuData);
    return response.data.data as Lieu;
  },

  /**
   * Deletes a location by ID (admin only).
   */
  deleteLieu: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/lieux/${id}`);
    return response.data.data as null;
  },

  /**
   * Retrieves schedules for a location.
   */
  getHoraires: async (id: number): Promise<HoraireLieu[]> => {
    const response = await axiosInstance.get(`/lieux/${id}/horaires`);
    return response.data.data as HoraireLieu[];
  },

  /**
   * Adds a new schedule to a location (admin only).
   */
  addHoraire: async (id: number, horaireData: Partial<HoraireLieu>): Promise<HoraireLieu> => {
    const response = await axiosInstance.post(`/lieux/${id}/horaires`, horaireData);
    return response.data.data as HoraireLieu;
  },

  /**
   * Updates a schedule for a location (admin only).
   */
  updateHoraire: async (id: number, horaireData: Partial<HoraireLieu>): Promise<HoraireLieu> => {
    const response = await axiosInstance.patch(`/lieux/${id}/horaires`, horaireData);
    return response.data.data as HoraireLieu;
  },

  /**
   * Deletes a schedule for a location (admin only).
   */
  deleteHoraire: async (id: number, horaireData: { id_horaire: number }): Promise<null> => {
    const response = await axiosInstance.delete(`/lieux/${id}/horaires`, { data: horaireData });
    return response.data.data as null;
  },
};

export default lieuService;