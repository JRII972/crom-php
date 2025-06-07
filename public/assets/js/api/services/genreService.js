import axiosInstance from '../axiosInstance';

/**
 * Service for handling genre-related API calls.
 */
const genreService = {
  /**
   * Retrieves a genre by its ID.
   */
  getGenre: async (id) => {
    const response = await axiosInstance.get(`/genres/${id}`);
    return response.data.data;
  },

  /**
   * Lists genres with optional search.
   */
  listGenres: async (keyword = '') => {
    const response = await axiosInstance.get('/genres', { params: { q: keyword } });
    return response.data.data;
  },

  /**
   * Creates a new genre (admin only).
   */
  createGenre: async (genreData) => {
    const response = await axiosInstance.post('/genres', genreData);
    return response.data.data;
  },

  /**
   * Updates a genre by ID (admin only).
   */
  updateGenre: async (id, genreData) => {
    const response = await axiosInstance.put(`/genres/${id}`, genreData);
    return response.data.data;
  },

  /**
   * Partially updates a genre by ID (admin only).
   */
  patchGenre: async (id, genreData) => {
    const response = await axiosInstance.patch(`/genres/${id}`, genreData);
    return response.data.data;
  },

  /**
   * Deletes a genre by ID (admin only).
   */
  deleteGenre: async (id) => {
    const response = await axiosInstance.delete(`/genres/${id}`);
    return response.data.data;
  },
};

export default genreService;
