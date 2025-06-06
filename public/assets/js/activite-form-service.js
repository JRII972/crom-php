/**
 * Service API pour la gestion des activites
 * Ce fichier montre comment intégrer la page avec votre backend
 */

// Exemple d'intégration avec votre API existante
import axiosInstance from '../../api/axiosInstance';

export class ActiviteFormService {
    
    /**
     * Récupère la liste des jeux pour Selectize
     */
    static async getJeux(searchTerm = '') {
        try {
            const response = await axiosInstance.get('/jeux', {
                params: {
                    search: searchTerm,
                    limit: 50
                }
            });
            return response.data;
        } catch (error) {
            console.error('Erreur lors du chargement des jeux:', error);
            // Retourner des données de démonstration en cas d'erreur
            return this.getDemoJeux();
        }
    }

    /**
     * Charge une activite pour édition
     */
    static async getActivite(id) {
        try {
            const response = await axiosInstance.get(`/activites/${id}`);
            return response.data;
        } catch (error) {
            console.error('Erreur lors du chargement de la activite:', error);
            throw error;
        }
    }

    /**
     * Crée une nouvelle activite
     */
    static async createActivite(formData) {
        try {
            const response = await axiosInstance.post('/activites', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            console.error('Erreur lors de la création de la activite:', error);
            throw error;
        }
    }

    /**
     * Met à jour une activite existante
     */
    static async updateActivite(id, formData) {
        try {
            const response = await axiosInstance.put(`/activites/${id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            console.error('Erreur lors de la mise à jour de la activite:', error);
            throw error;
        }
    }

    /**
     * Sauvegarde un brouillon
     */
    static async saveDraft(formData) {
        try {
            const response = await axiosInstance.post('/activites/draft', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            console.error('Erreur lors de la sauvegarde du brouillon:', error);
            throw error;
        }
    }

    /**
     * Upload d'une image
     */
    static async uploadImage(file) {
        try {
            const formData = new FormData();
            formData.append('image', file);
            
            const response = await axiosInstance.post('/upload/image', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data.url;
        } catch (error) {
            console.error('Erreur lors de l\'upload de l\'image:', error);
            throw error;
        }
    }

    /**
     * Données de démonstration
     */
    static getDemoJeux() {
        return [
            {
                id: 1,
                nom: 'Dungeons & Dragons 5e',
                description: 'Le célèbre jeu de rôle fantasy avec ses dragons, donjons et aventures épiques.',
                image: 'public/data/images/donjon&dragon.jpg',
                icon: 'public/data/icon/dnd_logo_big.webp',
                type_jeu: 'JDR'
            },
            {
                id: 2,
                nom: 'Pathfinder',
                description: 'Jeu de rôle fantasy avancé avec un système de règles complexe et détaillé.',
                image: 'public/data/images/Pathfinder.webp',
                icon: null,
                type_jeu: 'JDR'
            },
            {
                id: 3,
                nom: 'Cyberpunk RED',
                description: 'Jeu de rôle cyberpunk futuriste dans un monde dystopique high-tech.',
                image: 'public/data/images/Cyberpunk RED.png',
                icon: null,
                type_jeu: 'JDR'
            },
            {
                id: 4,
                nom: 'Chroniques Oubliées',
                description: 'JDR français médiéval-fantastique simple et accessible.',
                image: 'public/data/images/Chroniques_Oubliees.jpg',
                icon: null,
                type_jeu: 'JDR'
            },
            {
                id: 5,
                nom: 'Blades in the Dark',
                description: 'Jeu de rôle de voleurs dans une ville industrielle sombre.',
                image: 'public/data/images/Blades in the Dark.jpg',
                icon: null,
                type_jeu: 'JDR'
            },
            {
                id: 6,
                nom: 'Numenera',
                description: 'Science fantasy dans un futur lointain mystérieux.',
                image: 'public/data/images/Numenera.jpg',
                icon: null,
                type_jeu: 'JDR'
            },
            {
                id: 7,
                nom: 'Shadowrun',
                description: 'Cyberpunk avec magie et créatures fantastiques.',
                image: 'public/data/images/Shadowrun.jpg',
                icon: null,
                type_jeu: 'JDR'
            },
            {
                id: 8,
                nom: 'Warhammer 40k',
                description: 'Science fiction sombre dans un futur dystopique.',
                image: 'public/data/images/EGS_Warhammer_SpaceMarine2.jpeg',
                icon: 'public/data/icon/Warhammer40k.png',
                type_jeu: 'JDR'
            }
        ];
    }

    /**
     * Validation côté client des données de activite
     */
    static validateActiviteData(data) {
        const errors = [];

        // Validation du nom
        if (!data.nom || data.nom.trim().length === 0) {
            errors.push({ field: 'nom', message: 'Le nom de la activite est requis' });
        } else if (data.nom.trim().length > 255) {
            errors.push({ field: 'nom', message: 'Le nom ne peut pas dépasser 255 caractères' });
        }

        // Validation du jeu
        if (!data.id_jeu) {
            errors.push({ field: 'id_jeu', message: 'Le jeu est requis' });
        }

        // Validation du type de activite
        const typesActiviteValides = ['CAMPAGNE', 'ONESHOT', 'JEU_DE_SOCIETE', 'EVENEMENT'];
        if (!data.type_activite || !typesActiviteValides.includes(data.type_activite)) {
            errors.push({ field: 'type_activite', message: 'Type de activite invalide' });
        }

        // Validation du type de campagne
        if (data.type_activite === 'CAMPAGNE' && data.type_campagne) {
            const typesCampagneValides = ['OUVERTE', 'FERMEE'];
            if (!typesCampagneValides.includes(data.type_campagne)) {
                errors.push({ field: 'type_campagne', message: 'Type de campagne invalide' });
            }
        }

        // Validation de la description courte
        if (data.description_courte && data.description_courte.length > 140) {
            errors.push({ field: 'description_courte', message: 'La description courte ne peut pas dépasser 140 caractères' });
        }

        // Validation des nombres de joueurs
        const maxJoueurs = parseInt(data.nombre_max_joueurs) || 0;
        const maxSession = parseInt(data.max_joueurs_session) || 0;

        if (maxSession <= 0) {
            errors.push({ field: 'max_joueurs_session', message: 'Le nombre maximum de joueurs par session doit être supérieur à 0' });
        }

        if (maxSession > 50) {
            errors.push({ field: 'max_joueurs_session', message: 'Le nombre maximum de joueurs par session ne peut pas dépasser 50' });
        }

        if (maxJoueurs > 0 && maxSession > maxJoueurs) {
            errors.push({ 
                field: 'max_joueurs_session', 
                message: 'Le nombre de joueurs par session ne peut pas dépasser le nombre total maximum' 
            });
        }

        // Validation de l'URL d'image
        if (data.image && !this.isValidUrl(data.image) && !data.image.startsWith('public/')) {
            errors.push({ field: 'image', message: 'URL d\'image invalide' });
        }

        return errors;
    }

    /**
     * Valide une URL
     */
    static isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    /**
     * Formate les données pour l'envoi à l'API
     */
    static formatDataForApi(formData) {
        const data = {};
        
        // Convertir FormData en objet simple
        for (let [key, value] of formData.entries()) {
            if (key === 'verrouille') {
                data[key] = value === 'true' || value === true;
            } else if (key === 'nombre_max_joueurs' || key === 'max_joueurs_session') {
                data[key] = parseInt(value) || 0;
            } else if (value === '' || value === null) {
                data[key] = null;
            } else {
                data[key] = value;
            }
        }

        return data;
    }
}

// Export pour utilisation dans activite-form.js
window.ActiviteFormService = ActiviteFormService;
