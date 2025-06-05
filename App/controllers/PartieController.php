<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/BaseController.php';

use App\Controllers\Class\SessionDisplay;
use App\Controllers\Class\PartieDisplay;
use App\Database\Types\Genre;

class PartieController extends BaseController {
    /**
     * Display the partie page
     * 
     * @param int $id ID de la partie
     * @return string Rendered HTML
     */
    
    public function index(int $id) {
        // Utiliser la méthode sécurisée de PartieDisplay
        $partie = PartieDisplay::createSafe($id);
        
        // Si la partie n'existe pas, rediriger vers l'accueil
        if ($partie === null) {
            header('Location: /');
            exit;
        }
        
        $data = [
            'page_title' => 'Partie - ' . $partie->getNom(),
            'partie' => $partie,
            'jeu' => $partie->getJeu(),
            'maitre_de_jeu' => $partie->getMaitreJeu(),
            'sessions' => $partie->getSessions(), // TODO: Récupérer les sessions de cette partie
            'joueurs' => $partie->getJoueursInscrits(), // TODO: Récupérer les joueurs de cette partie
            'activeTab' => 'description'
        ];

        $this->addBreadcrumb($partie->getNom());       
        
        return $this->render('pages.partie', $data);
    }
}