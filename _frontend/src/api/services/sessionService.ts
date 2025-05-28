import axiosInstance from '../axiosInstance';
import type { Session, JoueurSession, Utilisateur } from '../types/db';

export interface SessionFilters {
  partie_id?: number;        // Filtrer par l'ID de la partie
  lieu_id?: number;          // Filtrer par l'ID du lieu
  date_debut?: string;       // Filtrer à partir d'une date (format YYYY-MM-DD)
  date_fin?: string;         // Filtrer jusqu'à une date (format YYYY-MM-DD)
  max_joueurs?: number;      // Filtrer par nombre max de joueurs
}

const sessionService = {
  getSession: async (id: number): Promise<Session> => {
    const response = await axiosInstance.get(`/sessions/${id}`);
    return response.data.data;
  },
  listSessions: async (filters: SessionFilters = {}): Promise<Session[]> => {
    const response = await axiosInstance.get('/sessions', { params: filters });
    return response.data.data;
  },
  createSession: async (sessionData: Partial<Session>): Promise<Session> => {
    const response = await axiosInstance.post('/sessions', sessionData);
    return response.data.data;
  },
  updateSession: async (id: number, sessionData: Partial<Session>): Promise<Session> => {
    const response = await axiosInstance.put(`/sessions/${id}`, sessionData);
    return response.data.data;
  },
  deleteSession: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/sessions/${id}`);
    return response.data.data;
  },
  getSessionPlayers: async (id: number): Promise<Utilisateur[]> => {
    const response = await axiosInstance.get(`/sessions/${id}/joueurs`);
    return response.data.data;
  },
  addPlayer: async (id: number, playerData: { id_utilisateur: string }): Promise<JoueurSession> => {
    const response = await axiosInstance.post(`/sessions/${id}/joueurs`, playerData);
    return response.data.data;
  },
  removePlayer: async (id: number, userId: string): Promise<null> => {
    const response = await axiosInstance.delete(`/sessions/${id}/joueurs/${userId}`);
    return response.data.data;
  },
};

export default sessionService;