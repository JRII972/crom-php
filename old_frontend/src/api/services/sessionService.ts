import axiosInstance from '../axiosInstance';
import type { Session, JoueurSession, Utilisateur } from '../types/db';

export interface SessionFilters {
  partie_id?: number;        // Filtrer par l'ID de la partie
  lieu_id?: number;          // Filtrer par l'ID du lieu
  date_debut?: string;       // Filtrer à partir d'une date (format YYYY-MM-DD)
  date_fin?: string;         // Filtrer jusqu'à une date (format YYYY-MM-DD)
  max_joueurs?: number;      // Filtrer par nombre max de joueurs
  categories?: string[];     // Filtrer par catégories (liste de mots-clés)
  jours?: number[];          // Filtrer par jours de la semaine (1=Dimanche, 2=Lundi, ..., 7=Samedi)
}

/**
 * Interface pour les options de filtres retournées par l'API
 */
export interface SessionFilterOptions {
  lieux: Array<{id: number, nom: string}>;
  date_debut: string;
  date_fin: string;
  max_joueurs: number;
  jours: number[];
  categories: string[];
  description?: {
    lieux: string;
    date_debut: string;
    date_fin: string;
    max_joueurs: string;
    jours: string;
    categories: string;
  };
}

const sessionService = {
  getSession: async (id: number): Promise<Session> => {
    const response = await axiosInstance.get(`/sessions/${id}`);
    return response.data.data;
  },
  listSessions: async (filters: SessionFilters = {}): Promise<Session[]> => {
    // Créer une copie des filtres pour ne pas modifier l'objet original
    const params: any = { ...filters };
    
    // Sérialiser les tableaux en JSON si présents
    if (params.categories && Array.isArray(params.categories)) {
      params.categories = JSON.stringify(params.categories);
    }
    
    if (params.jours && Array.isArray(params.jours)) {
      params.jours = JSON.stringify(params.jours);
    }
    
    const response = await axiosInstance.get('/sessions', { params });
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
  /**
   * Récupère les options de filtres disponibles pour les sessions
   * Utilise la méthode OPTIONS sur l'endpoint /sessions
   */
  getFilterOptions: async (): Promise<SessionFilterOptions> => {
    const response = await axiosInstance.options('/sessions');
    return response.data.data.filters;
  },
  removePlayer: async (id: number, userId: string): Promise<null> => {
    const response = await axiosInstance.delete(`/sessions/${id}/joueurs/${userId}`);
    return response.data.data;
  },
};

export default sessionService;