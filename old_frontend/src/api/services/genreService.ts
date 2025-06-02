import axiosInstance from '../axiosInstance';
import type { Genre } from '../types/db';

/**
 * Service for handling genre-related API calls, typé avec les interfaces.
 */
const genreService = {
  /**
   * Retrieves a genre by its ID.
   */
  getGenre: async (id: number): Promise<Genre> => {
    const response = await axiosInstance.get(`/genres/${id}`);
    return response.data.data as Genre;
  },

  /**
   * Lists genres with optional search.
   */
  listGenres: async (keyword = ''): Promise<Genre[]> => {
    const response = await axiosInstance.get('/genres', { params: { q: keyword } });
    return response.data.data as Genre[];
  },

  /**
   * Creates a new genre (admin only).
   */
  createGenre: async (genreData: Partial<Genre>): Promise<Genre> => {
    const response = await axiosInstance.post('/genres', genreData);
    return response.data.data as Genre;
  },

  /**
   * Updates a genre by ID (admin only).
   */
  updateGenre: async (id: number, genreData: Partial<Genre>): Promise<Genre> => {
    const response = await axiosInstance.put(`/genres/${id}`, genreData);
    return response.data.data as Genre;
  },

  /**
   * Partially updates a genre by ID (admin only).
   */
  patchGenre: async (id: number, genreData: Partial<Genre>): Promise<Genre> => {
    const response = await axiosInstance.patch(`/genres/${id}`, genreData);
    return response.data.data as Genre;
  },

  /**
   * Deletes a genre by ID (admin only).
   */
  deleteGenre: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/genres/${id}`);
    return response.data.data as null;
  },
};

export default genreService;