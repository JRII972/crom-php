<?php // /app/controllers/HomeController.php
// filepath: /var/www/html/App/controllers/HomeController.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/BaseController.php';

use App\Database\Types\Genre;
use App\Database\Types\Session;
use App\Database\Types\Partie;

class HomeController extends BaseController {
    /**
     * Display the home page
     * 
     * @return string Rendered HTML
     */
    
    public function index() {
        // Data to pass to the template
        $data = [
            'page_title' => 'CROM | BDR',
            'suggestion' => [
                'Fantaisie' => Session::search($this->pdo, categories:[Genre::search($this->pdo, 'fantasy')]),
                'Enquête' => Session::search($this->pdo, categories:[Genre::search($this->pdo, 'Enquête')]),
                'Coopératif' => Session::search($this->pdo, categories:[Genre::search($this->pdo, 'Coopératif')])
            ],
            'next_week' => Session::search($this->pdo, dateDebut:'2025-06-02', dateFin:'2025-06-08'),
        ];

        // Render the template
        return $this->render('pages.parties', $data);
    }
}