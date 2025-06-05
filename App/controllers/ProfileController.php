<?php
// Contrôleur pour la page de profil utilisateur
// filepath: /var/www/html/App/controllers/ProfileController.php

require_once __DIR__ . '/BaseController.php';
use Carbon\Carbon;

class ProfileController extends BaseController {
    
    /**
     * Affiche la page de profil
     * 
     * @param int|null $userId ID de l'utilisateur (facultatif, utilise l'utilisateur connecté par défaut)
     * @param string|null $activeTab Onglet actif (facultatif, utilise 'activites' par défaut)
     * @return string Rendu HTML de la page
     */
    public function show(?int $userId = null, ?string $activeTab = 'activites'): string {
        // Si l'utilisateur n'est pas spécifié, utiliser l'utilisateur connecté
        if (!$userId) {
            // Logique pour obtenir l'utilisateur connecté (à adapter selon l'authentification)
            $userId = $_SESSION['user_id'] ?? 1; // Exemple, à remplacer par la logique d'authentification réelle
        }
        
        // Récupérer les données de l'utilisateur (simulé pour l'exemple)
        $user = $this->getUserData($userId);
        
        // Récupérer les statistiques de l'utilisateur
        $stats = $this->getUserStats($userId);
        
        // Récupérer les activites de l'utilisateur
        $activites = $this->getUserActivites($userId);
        
        // Récupérer l'historique des activites
        $historique = $this->getUserHistorique($userId);
        
        // Récupérer les disponibilités
        $disponibilites = $this->getUserDisponibilites($userId);
        
        // Générer le calendrier du mois courant
        $calendar = $this->generateCalendar($userId);
        
        // Récupérer les préférences
        $preferences = $this->getUserPreferences($userId);
        
        // Récupérer les paiements
        $paiements = $this->getUserPaiements($userId);
        
        // Récupérer le statut d'adhésion
        $adhesion = $this->getUserAdhesion($userId);
        
        // URL du calendrier iCal
        $calendarUrl = 'https://lbdr-jdr.fr/calendar/ics/user/' . ($user->username ?? 'username');
        
        // Scripts spécifiques à la page de profil
        $scripts = [
            'profile.js' // Remplacez par les scripts réels dont vous avez besoin
        ];
        
        // Rendu du template avec toutes les données
        return $this->render('pages.profile-modular', [
            'user' => $user,
            'stats' => $stats,
            'activites' => $activites,
            'historique' => $historique,
            'disponibilites' => $disponibilites,
            'calendar' => $calendar,
            'preferences' => $preferences,
            'paiements' => $paiements,
            'adhesion' => $adhesion,
            'calendarUrl' => $calendarUrl,
            'activeTab' => $activeTab,
            'scripts' => $scripts,
            'pageTitle' => 'Mon Profil'
        ]);
    }
    
    /**
     * Simuler la récupération des données utilisateur
     * À remplacer par une vraie requête à la base de données
     */
    private function getUserData(int $userId) {
        // Exemple de données utilisateur simulées
        return (object) [
            'id' => $userId,
            'firstname' => 'Thomas',
            'lastname' => 'Dubois',
            'pseudo' => 'TomDss',
            'type' => 'Membre inscrit',
            'email' => 'thomas@email.com',
            'username' => 'tomdu92',
            'birthdate' => '1990-04-15',
            'discord' => 'TomD#1234',
            'profile_image' => 'https://picsum.photos/200',
            'created_at' => '2022-06-15',
            'gender' => 'M'
        ];
    }
    
    /**
     * Simuler la récupération des statistiques utilisateur
     */
    private function getUserStats(int $userId) {
        return (object) [
            'activites_jouees' => 42,
            'activites_creees' => 7,
            'pourcentage_activites' => 8
        ];
    }
    
    /**
     * Simuler la récupération des activites de l'utilisateur
     */
    private function getUserActivites(int $userId) {
        return [
            (object) [
                'id' => 1,
                'nom' => 'Les Ombres d\'Esteren',
                'type' => 'Campagne',
                'role' => 'Joueur',
                'prochaine_session' => '2025-06-15'
            ],
            (object) [
                'id' => 2,
                'nom' => 'Donjons & Dragons',
                'type' => 'OneShot',
                'role' => 'Maître du jeu',
                'prochaine_session' => '2025-06-22'
            ],
            (object) [
                'id' => 3,
                'nom' => 'Chroniques Oubliées',
                'type' => 'Campagne',
                'role' => 'Joueur',
                'prochaine_session' => '2025-06-29'
            ]
        ];
    }
    
    /**
     * Simuler la récupération de l'historique des activites
     */
    private function getUserHistorique(int $userId) {
        return [
            (object) [
                'id' => 4,
                'nom' => 'Appel de Cthulhu',
                'type' => 'OneShot',
                'type_slug' => 'oneshot',
                'date' => '2025-05-15',
                'role' => 'Joueur',
                'role_slug' => 'joueur',
                'lieu' => 'Salle Principale'
            ],
            (object) [
                'id' => 5,
                'nom' => 'Pathfinder',
                'type' => 'Campagne',
                'type_slug' => 'campagne',
                'date' => '2025-05-08',
                'role' => 'Maître du jeu',
                'role_slug' => 'mj',
                'lieu' => 'Salle 2'
            ],
            (object) [
                'id' => 6,
                'nom' => 'Dixit',
                'type' => 'Jeu de société',
                'type_slug' => 'jeu-societe',
                'date' => '2025-05-01',
                'role' => 'Joueur',
                'role_slug' => 'joueur',
                'lieu' => 'Salle 3'
            ]
        ];
    }
    
    /**
     * Simuler la récupération des disponibilités
     */
    private function getUserDisponibilites(int $userId) {
        return [
            (object) [
                'id' => 1,
                'jour' => 'Mercredi',
                'heure_debut' => '19:00',
                'heure_fin' => '23:00',
                'type' => 'Disponible'
            ],
            (object) [
                'id' => 2,
                'jour' => 'Vendredi',
                'heure_debut' => '20:00',
                'heure_fin' => '23:30',
                'type' => 'Disponible'
            ],
            (object) [
                'id' => 3,
                'jour' => 'Samedi',
                'heure_debut' => '14:00',
                'heure_fin' => '18:00',
                'type' => 'Disponible'
            ]
        ];
    }
    
    /**
     * Générer un calendrier pour le mois en cours
     */
    private function generateCalendar(int $userId) {
        $calendar = [];
        $date = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $availableDays = [3, 5, 6, 12, 13, 19, 20, 26, 27]; // Jours disponibles simulés
        
        while ($date->lte($endDate)) {
            $calendar[] = (object) [
                'number' => $date->day,
                'date' => $date->format('Y-m-d'),
                'available' => in_array($date->day, $availableDays)
            ];
            
            $date->addDay();
        }
        
        return $calendar;
    }
    
    /**
     * Simuler la récupération des préférences utilisateur
     */
    private function getUserPreferences(int $userId) {
        return (object) [
            'show_players' => true,
            'show_full_names' => true,
            'show_pseudos' => false,
            'custom_info' => true,
            'reminders' => true,
            'reminder_delay' => '1',
            'sync_mj' => true,
            'sync_player' => true,
            'sync_events' => true
        ];
    }
    
    /**
     * Simuler la récupération des paiements
     */
    private function getUserPaiements(int $userId) {
        return [
            (object) [
                'id' => 1,
                'date' => '2025-01-15',
                'description' => 'Adhésion Annuelle 2025',
                'montant' => 25.00,
                'statut' => 'Payé'
            ],
            (object) [
                'id' => 2,
                'date' => '2024-01-10',
                'description' => 'Adhésion Annuelle 2024',
                'montant' => 25.00,
                'statut' => 'Payé'
            ],
            (object) [
                'id' => 3,
                'date' => '2023-01-05',
                'description' => 'Adhésion Annuelle 2023',
                'montant' => 20.00,
                'statut' => 'Payé'
            ]
        ];
    }
    
    /**
     * Simuler la récupération du statut d'adhésion
     */
    private function getUserAdhesion(int $userId) {
        return (object) [
            'status' => 'active',
            'year' => '2025',
            'expiry_date' => '2025-12-31'
        ];
    }
}
