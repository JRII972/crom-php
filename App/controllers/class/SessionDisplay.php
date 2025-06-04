<?php

namespace App\Controllers\Class;

use App\Database\Types\Session;
use App\Database\Types\Partie;
use App\Database\Types\Lieu;
use App\Database\Types\Utilisateur;
use App\Controllers\Class\UtilisateurDisplay;
use App\Utils\Image;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use PDO;
use PDOException;

class SessionDisplay extends Session 
{
    private Image $displayImage;
    private JeuDisplay $jeuDisplay;

    /**
     * Constructeur de la classe SessionDisplay.
     *
     * @param int|null $id Identifiant de la session (si fourni, charge depuis la base)
     * @param Partie|int|null $partieOuId Objet Partie ou ID de la partie (requis si $id est null)
     * @param Lieu|int|null $lieuOuId Objet Lieu ou ID du lieu (requis si $id est null)
     * @param string|null $dateSession Date de la session (format Y-m-d, requis si $id est null)
     * @param string|null $heureDebut Heure de début (format H:i:s, requis si $id est null)
     * @param string|null $heureFin Heure de fin (format H:i:s, requis si $id est null)
     * @param Utilisateur|string|null $maitreJeuOuId Objet Utilisateur ou ID du maître du jeu (requis si $id est null)
     * @param int|null $maxJoueurs Nombre maximum de joueurs pour la session (optionnel)
     * @param int|null $maxJoueursSession Nombre maximum de joueurs par session (optionnel)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si la session n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Partie|int|null $partieOuId = null,
        Lieu|int|null $lieuOuId = null,
        ?string $dateSession = null,
        ?string $heureDebut = null,
        ?string $heureFin = null,
        Utilisateur|string|null $maitreJeuOuId = null,
        ?int $maxJoueurs = null,
        ?int $maxJoueursSession = null
    ) {
        parent::__construct(
            $id,
            $partieOuId,
            $lieuOuId,
            $dateSession,
            $heureDebut,
            $heureFin,
            $maitreJeuOuId,
            $maxJoueurs,
            $maxJoueursSession
        );

        $this->jeuDisplay = new JeuDisplay($this->getPartie()->getJeu()->getId());
        
        // Initialiser l'image d'affichage avec fallback
        $partieImage = $this->getPartie()->getImage();
        if ($partieImage !== null) {
            $this->displayImage = $partieImage;
        } else {
            $this->displayImage = $this->jeuDisplay->getImage();
        }
        
        // Initialiser JeuDisplay
    }

    /**
     * Recherche des sessions avec filtres optionnels.
     *
     * @param PDO $pdo Instance PDO
     * @param int $partieId ID de la partie (optionnel)
     * @param int $lieuId ID du lieu (optionnel)
     * @param string $dateDebut Date de début (format Y-m-d, optionnel)
     * @param string $dateFin Date de fin (format Y-m-d, optionnel)
     * @param int|null $maxJoueurs Nombre maximum de joueurs (optionnel)
     * @param array|null $categories Liste des catégories/genres pour filtrer (optionnel)
     * @param array|null $jours Liste des jours de la semaine pour filtrer (optionnel)
     * @param bool|null $serialize 
     * @return array Liste des sessions
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(
        PDO $pdo,
        ?int $partieId = 0,
        ?int $lieuId = 0,
        ?string $dateDebut = '',
        ?string $dateFin = '',
        ?int $maxJoueurs = null,
        ?array $categories = null,
        ?array $jours = null,
        ?bool $serialize = false
    ): array {
        $sessions = parent::search(
            $pdo,
            $partieId,
            $lieuId,
            $dateDebut,
            $dateFin,
            $maxJoueurs,
            $categories,
            $jours,
            true
        );
        
        // FIXME: relay cost effective a corriger rapidement
        $sessionDisplays = [];
        foreach ($sessions as $session) {
            $sessionDisplays[] = new self($session['id']);
        }
        
        return $sessionDisplays;
    }

    /**
     * Retourne le maître du jeu sous forme d'UtilisateurDisplay
     */
    public function getMaitreJeu(): UtilisateurDisplay {
        $mj = parent::getMaitreJeu();
        return new UtilisateurDisplay($mj->getId());
    }

    public function getImageURL(): string {
        return $this->displayImage->getFilePath();
    }

    public function getImageALT(): string {
        return $this->displayImage->getImageAlt();
    }

    public function getNomPartie(): string {
        return $this->getPartie()->getNom();
    }

    public function getNom(): string {
        return parent::getNom() ? parent::getNom() : $this->getNomPartie();
    }

    public function getTypeFormatted(): string {
        $partie = $this->getPartie();
        $typePartie = $partie->getTypePartie();
        
        // Enum pour adapter le type à la présentation utilisateur
        $typeDisplay = match($typePartie->value) {
            'CAMPAGNE' => 'Campagne',
            'ONESHOT' => 'One-Shot',
            'JEU_DE_SOCIETE' => 'Jeu de Société',
            'EVENEMENT' => 'Événement',
            default => $typePartie->value
        };
        
        // Si c'est une campagne, ajouter le type de campagne
        if ($typePartie->value === 'CAMPAGNE' && $partie->getTypeCampagne()) {
            $typeCampagne = match($partie->getTypeCampagne()->value) {
                'OUVERTE' => 'Ouverte',
                'FERMEE' => 'Fermée',
                default => $partie->getTypeCampagne()->value
            };
            $typeDisplay .= ' ' . $typeCampagne;
        }
        
        return $typeDisplay;
    }

    public function getTypeFormattedShort(): string {
        $partie = $this->getPartie();
        $typePartie = $partie->getTypePartie();
        
        // Enum pour adapter le type à la présentation utilisateur
        $typeDisplay = match($typePartie->value) {
            'CAMPAGNE' => 'Cmp',
            'ONESHOT' => '1Sht',
            'JEU_DE_SOCIETE' => 'JdS',
            'EVENEMENT' => 'Event',
            default => $typePartie->value
        };
        
        // Si c'est une campagne, ajouter le type de campagne
        if ($typePartie->value === 'CAMPAGNE' && $partie->getTypeCampagne()) {
            $typeCampagne = match($partie->getTypeCampagne()->value) {
                'OUVERTE' => 'O',
                'FERMEE' => 'F',
                default => $partie->getTypeCampagne()->value
            };
            $typeDisplay .= ' ' . $typeCampagne;
        }
        
        return $typeDisplay;
    }

    public function getLieuNom(): string {
        return parent::getLieu()->getNom();
    }

    public function getLieuShort(): string {
        return parent::getLieu()->getShortNom();
    }
    
    public function getNomJeu(): string {
        return $this->jeuDisplay->getNom();
    }

    public function getJoueurs()
    {
        $joueursSession = $this->getJoueursSession();
        $joueurs = [];
        foreach ($joueursSession as $joueurSession) {
            $joueurs[] = new UtilisateurDisplay($joueurSession->getIdUtilisateur()); // convertie en UtilisateurDisplay et renvoie la liste
        }
        return $joueurs;
    }

    public function isLocked():bool {
        return $this->getMaxJoueurs() <= $this->getNombreJoueursInscrits();
    }

}