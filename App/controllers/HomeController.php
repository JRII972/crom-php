<?php // /app/controllers/HomeController.php
// filepath: /var/www/html/App/controllers/HomeController.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/BaseController.php';

use App\Controllers\Class\SessionDisplay;
use App\Database\Types\Genre;
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
                'Fantaisie' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'fantasy'), serialize:false),
                'Enquête' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'Enquête'), serialize:false),
                'Coopératif' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'Coopératif'), serialize:false)
            ],
            'next_week' => [
                'Vendredi' => SessionDisplay::search($this->pdo, dateDebut:'2025-06-02', dateFin:'2025-06-08', serialize:false),
                'Samedi' => SessionDisplay::search($this->pdo, dateDebut:'2025-06-02', dateFin:'2025-06-08', serialize:false),
            ],
        ];
        
        // Render the template
        return $this->render('pages.parties', $data);
    }
}