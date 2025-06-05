<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/BaseController.php';

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
    
    public function index(int $id) {
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
            'joueurs' => $activite->getJoueursInscrits(), // TODO: Récupérer les joueurs de cette activite
            'activeTab' => 'description'
        ];

        $this->addBreadcrumb($activite->getNom());       
        
        return $this->render('pages.activite', $data);
    }
}