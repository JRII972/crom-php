import axiosInstance from '../axiosInstance';
import type { Partie, MembrePartie } from '../types/db';

export interface PartieFilters {
  q?: string;                // Recherche par mot-clé
  jeu_id?: number;           // Filtrer par l'ID du jeu
  mj?: string;               // Filtrer par l'ID du maître de jeu
  type_partie?: string;      // Filtrer par type de partie
  categories?: string[];     // Filtrer par catégories de jeu
  jours?: number[];          // Filtrer par jours de la semaine (1=Dimanche, 2=Lundi, ..., 7=Samedi)
  place_restante?: boolean;  // Filtrer pour les parties avec des places restantes
  verrouille?: boolean;      // Filtrer par parties verrouillées/déverrouillées
  order?: string;            // Trier les résultats
}

/**
 * Interface pour les options de filtres retournées par l'API
 */
export interface PartieFilterOptions {
  jeux: Array<{id: number, nom: string}>;
  categories: string[];
  jours: number[];
  types_parties: string[];
  description?: {
    jeux: string;
    categories: string;
    jours: string;
    types_parties: string;
  };
}

/**
 * Service for handling party-related API calls, typé avec les interfaces.
 */
const partieService = {
  /**
   * Retrieves a party by its ID.
   */
  getPartie: async (id: number): Promise<Partie> => {
    const response = await axiosInstance.get(`/parties/${id}`);
    return response.data.data as Partie;
  },

  /**
   * Lists parties with optional filters.
   */
  listParties: async (filters: PartieFilters = {}): Promise<Partie[]> => {
    // Créer une copie des filtres pour ne pas modifier l'objet original
    const params: any = { ...filters };
    
    // Sérialiser les tableaux en JSON si présents
    if (params.categories && Array.isArray(params.categories)) {
      params.categories = JSON.stringify(params.categories);
    }
    
    if (params.jours && Array.isArray(params.jours)) {
      params.jours = JSON.stringify(params.jours);
    }
    
    const response = await axiosInstance.get('/parties', { params });
    return response.data.data as Partie[];
  },

  /**
   * Creates a new party (game master or admin only).
   */
  createPartie: async (partieData: Partial<Partie>): Promise<Partie> => {
    const response = await axiosInstance.post('/parties', partieData);
    return response.data.data as Partie;
  },

  /**
   * Updates a party by ID (game master or admin only).
   */
  updatePartie: async (id: number, partieData: Partial<Partie>): Promise<Partie> => {
    const response = await axiosInstance.put(`/parties/${id}`, partieData);
    return response.data.data as Partie;
  },

  /**
   * Partially updates a party by ID (game master or admin only).
   */
  patchPartie: async (id: number, partieData: Partial<Partie>): Promise<Partie> => {
    const response = await axiosInstance.patch(`/parties/${id}`, partieData);
    return response.data.data as Partie;
  },

  /**
   * Deletes a party by ID (game master or admin only).
   */
  deletePartie: async (id: number): Promise<null> => {
    const response = await axiosInstance.delete(`/parties/${id}`);
    return response.data.data as null;
  },

  /**
   * Récupère les options de filtres disponibles pour les parties
   * Utilise la méthode OPTIONS sur l'endpoint /parties
   */
  getFilterOptions: async (): Promise<PartieFilterOptions> => {
    const response = await axiosInstance.options('/parties');
    return response.data.data.filters;
  },
};

export default partieService;