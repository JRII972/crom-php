<?php
// Contrôleur pour la page de connexion
// filepath: /var/www/html/App/controllers/AuthController.php

require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController {
    
    /**
     * Affiche la page de connexion
     * 
     * @return string Rendu HTML de la page
     */
    public function index(): string {
        // Vérifier si l'utilisateur est déjà connecté
        if (isset($_SESSION['user_id'])) {
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
