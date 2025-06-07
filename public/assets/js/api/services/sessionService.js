import axiosInstance from '../axiosInstance';

const sessionService = {
  getSession: async (id) => {
    const response = await axiosInstance.get(`/sessions/${id}`);
    return response.data.data;
  },
  
  listSessions: async (filters = {}) => {
    const response = await axiosInstance.get('/sessions', { params: filters });
    return response.data.data;
  },
  
  createSession: async (sessionData) => {
    const response = await axiosInstance.post('/sessions', sessionData);
    return response.data.data;
  },
  
  updateSession: async (id, sessionData) => {
    const response = await axiosInstance.put(`/sessions/${id}`, sessionData);
    return response.data.data;
  },
  
  deleteSession: async (id) => {
    const response = await axiosInstance.delete(`/sessions/${id}`);
    return response.data.data;
  },
  
  getSessionPlayers: async (id) => {
    const response = await axiosInstance.get(`/sessions/${id}/joueurs`);
    return response.data.data;
  },
  
  addPlayer: async (id, playerData) => {
    const response = await axiosInstance.post(`/sessions/${id}/joueurs`, playerData);
    return response.data.data;
  },
  
  removePlayer: async (id, userId) => {
    const response = await axiosInstance.delete(`/sessions/${id}/joueurs/${userId}`);
    return response.data.data;
  },
};

export default sessionService;
