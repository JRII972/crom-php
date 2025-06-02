<?php // /app/controllers/HomeController.php
// filepath: /var/www/html/App/controllers/HomeController.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    /**
     * Display the home page
     * 
     * @return string Rendered HTML
     */
    public function index() {
        // Data to pass to the template
        $data = [
            'page_title' => 'Accueil - Blade',
            'message' => 'Bienvenue sur mon site !',
            'current_date' => date('Y-m-d H:i:s')
        ];

        // Render the template
        return $this->render('pages.home', $data);
    }
}