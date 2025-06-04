<?php

declare(strict_types=1);

/**
 * Configuration centralisée des caches pour toutes les classes App\Database\Types
 * 
 * Cette configuration permet de contrôler l'activation et la durée de tous les caches
 * utilisés dans les classes de types de base de données.
 */

return [
    // Configuration globale du cache
    'global' => [
        'enabled' => true,      // Activer/désactiver tous les caches
        'default_ttl' => 300,   // TTL par défaut : 5 minutes (300 secondes)
        'extension_required' => 'apcu', // Extension requise pour le cache
    ],

    // Configuration spécifique par classe
    'classes' => [
        'Session' => [
            'enabled' => true,
            'ttl' => 300,           // 5 minutes
            'prefix' => 'session_search_',
            'description' => 'Cache pour les recherches de sessions'
        ],
        
        'Partie' => [
            'enabled' => true,
            'ttl' => 600,           // 10 minutes
            'prefix' => 'partie_search_',
            'description' => 'Cache pour les recherches de parties'
        ],
        
        'Jeu' => [
            'enabled' => true,
            'ttl' => 1800,          // 30 minutes
            'prefix' => 'jeu_search_',
            'description' => 'Cache pour les recherches de jeux'
        ],
        
        'Utilisateur' => [
            'enabled' => true,
            'ttl' => 900,           // 15 minutes
            'prefix' => 'utilisateur_search_',
            'description' => 'Cache pour les recherches d\'utilisateurs'
        ],
        
        'Lieu' => [
            'enabled' => true,
            'ttl' => 3600,          // 1 heure
            'prefix' => 'lieu_search_',
            'description' => 'Cache pour les recherches de lieux'
        ],
        
        'Genre' => [
            'enabled' => true,
            'ttl' => 3600,          // 1 heure
            'prefix' => 'genre_search_',
            'description' => 'Cache pour les recherches de genres'
        ],
        
        'Evenement' => [
            'enabled' => true,
            'ttl' => 600,           // 10 minutes
            'prefix' => 'evenement_search_',
            'description' => 'Cache pour les recherches d\'événements'
        ],
        
        'HorairesLieu' => [
            'enabled' => true,
            'ttl' => 1800,          // 30 minutes
            'prefix' => 'horaires_lieu_search_',
            'description' => 'Cache pour les recherches d\'horaires de lieux'
        ],
        
        'JoueursSession' => [
            'enabled' => true,
            'ttl' => 300,           // 5 minutes
            'prefix' => 'joueurs_session_search_',
            'description' => 'Cache pour les recherches de joueurs par session'
        ],
        
        'MembrePartie' => [
            'enabled' => true,
            'ttl' => 600,           // 10 minutes
            'prefix' => 'membre_partie_search_',
            'description' => 'Cache pour les recherches de membres de parties'
        ],
        
        'CreneauxUtilisateur' => [
            'enabled' => true,
            'ttl' => 900,           // 15 minutes
            'prefix' => 'creneaux_utilisateur_search_',
            'description' => 'Cache pour les recherches de créneaux utilisateur'
        ],
        
        'PeriodeAssociation' => [
            'enabled' => true,
            'ttl' => 3600,          // 1 heure
            'prefix' => 'periode_association_search_',
            'description' => 'Cache pour les recherches de périodes d\'association'
        ],
        
        'NotificationsHelloasso' => [
            'enabled' => true,
            'ttl' => 180,           // 3 minutes
            'prefix' => 'notifications_helloasso_search_',
            'description' => 'Cache pour les recherches de notifications HelloAsso'
        ],
        
        'PaiementsHelloasso' => [
            'enabled' => true,
            'ttl' => 300,           // 5 minutes
            'prefix' => 'paiements_helloasso_search_',
            'description' => 'Cache pour les recherches de paiements HelloAsso'
        ],
        
        'DefaultDatabaseType' => [
            'enabled' => true,
            'ttl' => 300,           // 5 minutes
            'prefix' => 'default_search_',
            'description' => 'Cache par défaut pour les types de base de données'
        ]
    ],

    // Configuration des environnements
    'environments' => [
        'development' => [
            'force_disable' => false,  // Forcer la désactivation en développement
            'debug_mode' => true,       // Mode debug pour logs de cache
        ],
        'testing' => [
            'force_disable' => true,   // Désactiver en test
            'debug_mode' => false,
        ],
        'production' => [
            'force_disable' => false,
            'debug_mode' => false,
        ]
    ],

    // Gestion de l'invalidation
    'invalidation' => [
        'auto_invalidate_related' => true,     // Invalider automatiquement les caches liés
        'invalidation_patterns' => [
            'Session' => ['Partie', 'JoueursSession'],  // Invalider ces caches quand Session change
            'Partie' => ['Session', 'MembrePartie'],    // Invalider ces caches quand Partie change
            'Utilisateur' => ['JoueursSession', 'MembrePartie', 'CreneauxUtilisateur'],
            'Lieu' => ['Session', 'HorairesLieu'],
            'Jeu' => ['Partie'],
            'Genre' => ['Jeu', 'Partie']
        ]
    ]
];
