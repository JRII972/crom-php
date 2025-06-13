<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\Class\SessionDisplay;
use App\Controllers\Class\ActiviteDisplay;
use App\Database\Types\Genre;

class ActiviteController extends BaseController {
    /**
     * Display the activite page
     * 
     * @param int $id ID de la activite
     * @return string Rendered HTML
     */
    
    public function index(int $id): string {
        // Utiliser la méthode sécurisée de ActiviteDisplay
        $activite = ActiviteDisplay::createSafe($id);
        
        // Si la activite n'existe pas, rediriger vers l'accueil
        if ($activite === null) {
            header('Location: /');
            exit;
        }
        
        $data = [
            'page_title' => 'Activite - ' . $activite->getNom(),
            'activite' => $activite,
            'jeu' => $activite->getJeu(),
            'maitre_de_jeu' => $activite->getMaitreJeu(),
            'sessions' => $activite->getSessions(), // TODO: Récupérer les sessions de cette activite
            'nextSessions' => $activite->getNextSessions(), // TODO: Récupérer les sessions de cette activite
            'joueurs' => $activite->getJoueursInscrits(), // TODO: Récupérer les joueurs de cette activite
            'activeTab' => 'description',

            'modules' => [
                'activite-detail.js'
            ]
        ];

        $this->addBreadcrumb($activite->getNom());       
        
        return $this->render('pages.activite', $data);
    }

    public function create()
    {
        return $this->render('pages.form.activite-form', [
            'title' => 'Créer une nouvelle activite',

            'modules' => [
                'activite-form.js'
            ]
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une activité
     */
    public function edit($id)
    {
        $activite = ActiviteDisplay::createSafe($id);
        
        // Si la activite n'existe pas, rediriger vers l'accueil
        if ($activite === null) {
            header('Location: /');
            exit;
        }
        
        return $this->render('pages.form.activite-form', [
            'title' => 'Modifier une activite',
            'activite' => $activite,

            'modules' => [
                'activite-form.js'
            ]
        ]);
    }

    /**
     * Traite la création d'une activité
     */
    public function store()
    {
        // TODO: validation et création
    }

    /**
     * Traite la modification d'une activité
     */
    public function update($id)
    {
        // TODO: validation et mise à jour
    }
}