// Enumérations
export type Sexe = 'M' | 'F' | 'Autre';
export type TypeUtilisateur = 'NON_INSCRIT' | 'INSCRIT' | 'ADMINISTRATEUR';
export type TypeJeu = 'JDR' | 'JEU_DE_SOCIETE' | 'AUTRE';
export type TypePartie = 'CAMPAGNE' | 'ONESHOT' | 'JEU_DE_SOCIETE' | 'EVENEMENT';
export type TypeCampagne = 'OUVERTE' | 'FERMEE';
export type TypeCreneau = 'DISPONIBILITE' | 'INDISPONIBILITE';
export type TypeRecurrence = 'AUCUNE' | 'QUOTIDIENNE' | 'HEBDOMADAIRE' | 'MENSUELLE' | 'ANNUELLE';

// Utilisateur (private_jsonSerialize)
export interface Utilisateur {
  id: string;
  prenom: string;
  nom: string;
  email: string|null;
  login: string;
  date_de_naissance: string|null;
  sexe: Sexe;
  id_discord: string|null;
  pseudonyme: string|null;
  image: string|null;
  type_utilisateur: TypeUtilisateur;
  date_inscription: string|null;
  ancien_utilisateur: boolean;
  premiere_connexion: boolean;
  date_creation: string;
  age: number|null;
  annees_anciennete: number;
}

// Genre
export interface Genre {
  id: number;
  nom: string;
}

// Jeu
export interface Jeu {
  id: number;
  nom: string;
  description: string|null;
  type_jeu: TypeJeu;
  genres: Genre[];
}

// Partie
export interface Partie {
  id: number;
  id_jeu: number;
  id_maitre_jeu: string;
  jeu: Jeu|null;
  maitre_jeu: Utilisateur|null;
  type_partie: TypePartie;
  type_campagne?: TypeCampagne;
  description_courte?: string|null;
  description?: string|null;
  nombre_max_joueurs: number;
  max_joueurs_session: number;
  verrouille: boolean;
  image?: string|null;
  texte_alt_image?: string|null;
  date_creation: string;
}

// Session
export interface Session {
  id: number;
  id_partie: number;
  partie: Partie|null;
  id_lieu: number;
  lieu: Lieu|null;
  date_session: string;
  heure_debut: string;
  heure_fin: string;
  id_maitre_jeu: string;
  maitre_jeu: Utilisateur|null;
  nombre_max_joueurs: number|null;
  max_joueurs_session: number;
  nombre_joueurs_session: number;
}

// Lieu
export interface Lieu {
  id: number;
  nom: string;
  adresse: string|null;
  latitude: number|null;
  longitude: number|null;
  description: string|null;
}

// Créneau utilisateur
export interface CreneauUtilisateur {
  id: number;
  id_utilisateur: string;
  type_creneau: TypeCreneau;
  date_heure_debut: string;
  date_heure_fin: string;
  est_recurrant: boolean;
  regle_recurrence: string|null;
}

// Membre de partie
export interface MembrePartie {
  id_partie: number;
  id_utilisateur: string;
}

// Joueur inscrit à une session
export interface JoueurSession {
  id_session: number;
  id_utilisateur: string;
  date_inscription: string;
}

// Période d'association
export interface PeriodeAssociation {
  id: number;
  date_debut: string;
  date_fin: string;
}

// Paiement HelloAsso
export interface PaiementHelloasso {
  id: string;
  id_notification: string|null;
  id_utilisateur: string|null;
  notification: NotificationHelloasso|null;
  utilisateur: Utilisateur|null;
  type_paiement: string|null;
  nom: string|null;
  montant: number;
  devise: string;
  date_echeance: string|null;
  statut: string|null;
  metadonnees: string|null;
  date_creation: string;
}

// Notification HelloAsso
export interface NotificationHelloasso {
  id: string;
  type_evenement: string;
  date_evenement: string;
  donnees: string;
  date_reception: string;
  traite: boolean;
}

// Horaires de lieu
export interface HoraireLieu {
  id: number;
  id_lieu: number;
  heure_debut: string;
  heure_fin: string;
  type_recurrence: TypeRecurrence;
  regle_recurrence: any; // JSON object or null
  exceptions: any; // JSON array or null
  id_evenement: number|null;
}

// Événement
export interface Evenement {
  id: number;
  nom: string;
  description: string|null;
  date_debut: string;
  date_fin: string;
  id_lieu: number|null;
  regle_recurrence: string|null;
  exceptions: string|null;
  date_creation: string;
}