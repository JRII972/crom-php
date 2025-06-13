<?php // /app/controllers/HomeController.php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/BaseController.php';

use App\Controllers\Class\SessionDisplay;
use App\Controllers\Class\ActiviteDisplay;
use App\Database\Types\Genre;
use App\Database\Types\Activite;
use App\Database\Types\TypeActivite;
use App\Database\Types\Utilisateur;

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
                // 'Fantaisie' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'fantasy'), serialize:false),
                'Enquête' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'Enquête'), serialize:false),
                'Coopératif' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'Coopératif'), serialize:false)
            ],
            'next_week' => [
                'Vendredi' => SessionDisplay::search($this->pdo, dateDebut:'2025-06-02', dateFin:'2025-06-08', serialize:false),
                'Samedi' => SessionDisplay::search($this->pdo, dateDebut:'2025-06-02', dateFin:'2025-06-08', serialize:false),
            ],
        ];
        
        // Render the template
        return $this->render('pages.activites', $data);
    }

    public function allActivites() {
        // Récupération du paramètre GET group_by
        $groupBy = $_GET['group_by'] ?? 'semaine';
        
        // Data de base pour les suggestions
        $suggestion = [
            'Fantaisie' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'fantasy'), serialize:false),
            'Enquête' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'Enquête'), serialize:false),
            'Coopératif' => SessionDisplay::search($this->pdo, categories:Genre::search($this->pdo, 'Coopératif'), serialize:false)
        ];
        
        // Générer les sections en fonction du paramètre group_by
        $sections = [];
        
        switch ($groupBy) {
            case 'categorie':
                // Groupement par catégorie - utilise ActiviteDisplay
                $sections = $this->groupByCategorie();
                break;
                
            case 'type':
                // Groupement par type de activite - utilise ActiviteDisplay
                $sections = $this->groupByType();
                break;
                
            case 'semaine':
            default:
                // Groupement par semaine - utilise SessionDisplay (défaut)
                $sections = $this->groupBySemaine();
                break;
        }
        
        $data = [
            'page_title' => 'CROM | BDR',
            'suggestion' => $suggestion,
            'sections' => $sections,
        ];
        
        // Render the template
        return $this->render('pages.all-activites', $data);
    }
    
    /**
     * Groupe les activites par catégorie (genres)
     * Retourne une liste de ActiviteDisplay
     */
    private function groupByCategorie(): array {
        $sections = [];
        
        // Récupérer toutes les catégories (genres)
        $genres = Genre::search($this->pdo);
        
        foreach ($genres as $genre) {
            $genreNom = is_array($genre) ? $genre['nom'] : $genre->getNom();
            
            // Rechercher les activites de cette catégorie
            $activites = ActiviteDisplay::search(
                $this->pdo, 
                categories: [$genre]
            );
            if (!empty($activites)) {
                $sections[''][$genreNom] = $activites;
            }
        }
        
        return $sections;
    }
    
    /**
     * Groupe les activites par type (TypeActivite)
     * Retourne une liste de ActiviteDisplay
     */
    private function groupByType(): array {
        $sections = [];
        
        // Types de activites possibles
        $types = [
            TypeActivite::Campagne->value => 'Campagnes',
            TypeActivite::Oneshot->value => 'One-Shots',
            TypeActivite::JeuDeSociete->value => 'Jeux de Société',
            TypeActivite::Evenement->value => 'Événements'
        ];
        
        foreach ($types as $typeValue => $typeLabel) {
            // Rechercher les activites de ce type
            $activites = ActiviteDisplay::search(
                $this->pdo,
                typeActivite: $typeValue
            );
            
            if (!empty($activites)) {
                foreach ($activites as $activite) {
                    $sections[$typeLabel][$activite->getTypeCampagne()->name ?? ''][] = $activite;
                }
            }
        }
        
        return $sections;
    }
    
    /**
     * Groupe les sessions par semaine
     * Retourne une liste de SessionDisplay
     */
    private function groupBySemaine(): array {
        $sections = [];
        
        // Calculer les dates
        $dateActuelle = new \DateTime();
        
        // Semaine actuelle
        $debutSemaineActuelle = clone $dateActuelle;
        $debutSemaineActuelle->modify('monday this week');
        $finSemaineActuelle = clone $debutSemaineActuelle;
        $finSemaineActuelle->modify('+6 days');
        
        $semaineActuelle = $this->getJoursSemaine($debutSemaineActuelle);
        if (!empty(array_filter($semaineActuelle))) {
            $sections['Cette semaine (' . $debutSemaineActuelle->format('d/m') . ' - ' . $finSemaineActuelle->format('d/m') . ')'] = $semaineActuelle;
        }
        
        // Semaine prochaine
        $debutSemaineProcheeine = clone $dateActuelle;
        $debutSemaineProcheeine->modify('next monday');
        $finSemaineProcheeine = clone $debutSemaineProcheeine;
        $finSemaineProcheeine->modify('+6 days');
        
        $semaineProcheeine = $this->getJoursSemaine($debutSemaineProcheeine);
        if (!empty(array_filter($semaineProcheeine))) {
            $sections['Semaine prochaine (' . $debutSemaineProcheeine->format('d/m') . ' - ' . $finSemaineProcheeine->format('d/m') . ')'] = $semaineProcheeine;
        }
        
        // Toutes les autres semaines futures (jusqu'à 8 semaines)
        $dateDebut = clone $finSemaineProcheeine;
        $dateDebut->modify('+1 day'); // Lundi de la semaine suivante
        
        for ($i = 0; $i < 8; $i++) {
            $debutSemaine = clone $dateDebut;
            $debutSemaine->modify("+{$i} weeks");
            $finSemaine = clone $debutSemaine;
            $finSemaine->modify('+6 days');
            
            $semaine = $this->getJoursSemaine($debutSemaine);
            if (!empty(array_filter($semaine))) {
                $sections['Semaine du ' . $debutSemaine->format('d/m') . ' - ' . $finSemaine->format('d/m')] = $semaine;
            }
        }
        
        return $sections;
    }
    
    /**
     * Récupère les sessions pour chaque jour d'une semaine
     * Ne retourne que les jours avec des sessions
     */
    private function getJoursSemaine(\DateTime $debutSemaine): array {
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $semaine = [];
        
        $dateJour = clone $debutSemaine;
        foreach ($jours as $nomJour) {
            $sessions = SessionDisplay::search(
                $this->pdo, 
                dateDebut: $dateJour->format('Y-m-d'), 
                dateFin: $dateJour->format('Y-m-d'), 
                serialize: false
            );
            
            // N'ajouter que les jours avec des sessions
            if (!empty($sessions)) {
                $semaine[$nomJour] = $sessions;
            }
            
            $dateJour->modify('+1 day');
        }
        
        return $semaine;
    }

}