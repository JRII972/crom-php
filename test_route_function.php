<?php

require_once __DIR__ . '/App/controllers/BaseController.php';

// Classe de test qui étend BaseController
class TestRouteController extends BaseController {
    public function testRoute() {
        // Test des breadcrumbs
        $this->setBreadcrumbs([
            [
                'titre' => 'Accueil',
                'location' => '/'
            ],
            [
                'titre' => 'Parties',
                'location' => '/parties.php'
            ],
            [
                'titre' => 'Partie Test'
            ]
        ]);

        // Données de test avec une session mock
        $sessionData = (object) [
            'getPartie' => function() {
                return (object) [
                    'getId' => function() { return 123; },
                    'getNom' => function() { return 'Partie de test'; },
                    'getDescription' => function() { return 'Description de la partie de test'; }
                ];
            },
            'getImageURL' => function() { return '/assets/images/test.jpg'; },
            'getImageALT' => function() { return 'Image de test'; },
            'getNomJeu' => function() { return 'Jeu de test'; },
            'getMaitreJeu' => function() {
                return (object) [
                    'displayName' => function() { return 'Maître de jeu test'; }
                ];
            },
            'getTypeFormatted' => function() { return 'One-shot'; },
            'getTypeFormattedShort' => function() { return 'OS'; },
            'isLocked' => function() { return false; },
            'getNombreJoueursInscrits' => function() { return 3; },
            'getMaxJoueurs' => function() { return 5; },
            'getLieuNom' => function() { return 'Lieu de test'; },
            'getLieuShort' => function() { return 'LT'; },
            'getJoueurs' => function() {
                return [
                    (object) ['displayName' => function() { return 'Joueur 1'; }],
                    (object) ['displayName' => function() { return 'Joueur 2'; }],
                    (object) ['displayName' => function() { return 'Joueur 3'; }]
                ];
            }
        ];

        $html = $this->render('test_route', [
            'session' => $sessionData
        ]);
        
        echo $html;
    }
}

try {
    $controller = new TestRouteController();
    $controller->testRoute();
} catch (Exception $e) {
    echo "<h1>Erreur:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
