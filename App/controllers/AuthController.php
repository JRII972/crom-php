<?php
// Contrôleur pour la page de connexion

namespace App\Controllers;

require_once __DIR__ . '/../Utils/helpers.php';

class AuthController extends BaseController {
    
    /**
     * Affiche la page de connexion
     * 
     * @return string Rendu HTML de la page
     */
    public function index(): string {
        // Vérifier si l'utilisateur est déjà connecté via les cookies JWT
        $authenticatedUser = getAuthenticatedUser();
        
        if ($authenticatedUser) {
            // Rediriger vers le tableau de bord ou la page d'accueil
            if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
                header('Location: ' . $_GET['redirect']);
            } else {
                header('Location: /');
            }
            exit;
        }
        
        // Rendu du template de connexion
        return $this->render('pages.login-modular', [
            'pageTitle' => 'Connexion'
        ]);
    }
    

}
