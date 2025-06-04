<?php

namespace App\Controllers\Class;

use App\Database\Types\Partie;
use App\Database\Types\Jeu;
use App\Database\Types\Utilisateur;
use App\Utils\Image;
use PDO;
use PDOException;

class PartieDisplay extends Partie 
{
    private ?Image $displayImage = null;

    /**
     * Constructeur de la classe PartieDisplay.
     * Gère automatiquement les erreurs de partie inexistante.
     *
     * @param int|null $id Identifiant de la partie
     * @param Jeu|int|null $jeuOuId Objet Jeu ou ID du jeu
     * @param string|null $nom Nom de la partie
     * @param string|null $description Description de la partie
     * @param Image|string|array|null $image Image de la partie
     * @param \App\Database\Types\TypePartie|null $typePartie Type de partie
     * @param \App\Database\Types\TypeCampagne|null $typeCampagne Type de campagne
     * @param Utilisateur|string|null $maitreJeuOuId Maître du jeu
     * @param int|null $maxJoueurs Nombre maximum de joueurs
     * @param bool|null $estPublique Si la partie est publique
     * @param bool|null $estActive Si la partie est active
     * @throws PDOException Si la partie n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Jeu|int|null $jeuOuId = null,
        ?string $nom = null,
        ?string $description = null,
        Image|string|array|null $image = null,
        ?\App\Database\Types\TypePartie $typePartie = null,
        ?\App\Database\Types\TypeCampagne $typeCampagne = null,
        Utilisateur|string|null $maitreJeuOuId = null,
        ?int $maxJoueurs = null,
        ?bool $estPublique = null,
        ?bool $estActive = null
    ) {
        parent::__construct(
            $id,
            $jeuOuId,
            $nom,
            $description,
            $image,
            $typePartie,
            $typeCampagne,
            $maitreJeuOuId,
            $maxJoueurs,
            $estPublique,
            $estActive
        );

        // Initialiser l'image d'affichage avec fallback
        $partieImage = parent::getImage();
        if ($partieImage !== null) {
            $this->displayImage = $partieImage;
        } else {
            // Fallback vers l'image du jeu
            $jeu = $this->getJeu();
            if ($jeu && $jeu->getImage()) {
                $this->displayImage = $jeu->getImage();
            }
        }
    }

    /**
     * Méthode statique pour créer une PartieDisplay de manière sécurisée.
     * Retourne null si la partie n'existe pas au lieu de lever une exception.
     *
     * @param int $id Identifiant de la partie
     * @return PartieDisplay|null La partie ou null si elle n'existe pas
     */
    public static function createSafe(int $id): ?PartieDisplay
    {
        try {
            return new self($id);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Vérifie si une partie existe dans la base de données.
     *
     * @param PDO $pdo Instance PDO
     * @param int $id Identifiant de la partie
     * @return bool True si la partie existe, false sinon
     */
    public static function exists(PDO $pdo, int $id): bool
    {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM partie WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Recherche des parties avec filtres optionnels.
     * Compatible avec la signature de la classe parent.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé pour la recherche (optionnel)
     * @param int $idJeu ID du jeu (optionnel)
     * @param string $idMaitreJeu ID du maître du jeu (optionnel)
     * @param string $typePartie Type de partie (optionnel)
     * @param array|null $categories Liste des catégories/genres pour filtrer (optionnel)
     * @param array|null $jours Liste des jours de la semaine pour filtrer (optionnel)
     * @return array Liste des parties
     */
    public static function search(
        PDO $pdo,
        string $keyword = '',
        int $idJeu = 0,
        string $idMaitreJeu = '',
        string $typePartie = '',
        ?array $categories = null,
        ?array $jours = null
    ): array {
        $parties = parent::search(
            $pdo,
            $keyword,
            $idJeu,
            $idMaitreJeu,
            $typePartie,
            $categories,
            $jours
        );
        
        $partieDisplays = [];
        foreach ($parties as $partie) {
            $partieDisplays[] = new self($partie['id']);
        }
        
        return $partieDisplays;
    }

    /**
     * Retourne le maître du jeu 
     */
    public function getMaitreJeu(): ?UtilisateurDisplay {
        return new UtilisateurDisplay($this->idMaitreJeu);
    }

    /**
     * Retourne le type de partie formaté pour l'affichage
     */
    public function getTypeFormatted(): string {
        $typePartie = $this->getTypePartie();
        
        $typeDisplay = match($typePartie->value) {
            'CAMPAGNE' => 'Campagne',
            'ONESHOT' => 'One-Shot',
            'JEU_DE_SOCIETE' => 'Jeu de Société',
            'EVENEMENT' => 'Événement',
            default => $typePartie->value
        };
        
        // Si c'est une campagne, ajouter le type de campagne
        if ($typePartie->value === 'CAMPAGNE' && $this->getTypeCampagne()) {
            $typeCampagne = match($this->getTypeCampagne()->value) {
                'OUVERTE' => 'Ouverte',
                'FERMEE' => 'Fermée',
                default => $this->getTypeCampagne()->value
            };
            $typeDisplay .= ' ' . $typeCampagne;
        }
        
        return $typeDisplay;
    }

    /**
     * Retourne le type de partie formaté en version courte
     */
    public function getTypeFormattedShort(): string {
        $typePartie = $this->getTypePartie();
        
        $typeDisplay = match($typePartie->value) {
            'CAMPAGNE' => 'Cmp',
            'ONESHOT' => '1Sht',
            'JEU_DE_SOCIETE' => 'JdS',
            'EVENEMENT' => 'Event',
            default => $typePartie->value
        };
        
        // Si c'est une campagne, ajouter le type de campagne
        if ($typePartie->value === 'CAMPAGNE' && $this->getTypeCampagne()) {
            $typeCampagne = match($this->getTypeCampagne()->value) {
                'OUVERTE' => 'O',
                'FERMEE' => 'F',
                default => $this->getTypeCampagne()->value
            };
            $typeDisplay .= ' ' . $typeCampagne;
        }
        
        return $typeDisplay;
    }

    /**
     * Retourne le nom du jeu associé à la partie
     */
    public function getNomJeu(): string {
        $jeu = $this->getJeu();
        return $jeu ? $jeu->getNom() : 'Jeu inconnu';
    }

    /**
     * Retourne le lieu par défaut pour l'affichage de la partie.
     * Si la partie a des sessions, retourne le lieu de la première session.
     * Sinon, retourne un lieu par défaut.
     */
    public function getLieu(): string {
        $sessions = $this->getSessions();
        if (!empty($sessions)) {
            $lieu = $sessions[0]->getLieu();
            if ($lieu) {
                return $lieu->getNom();
            }
        }
        return 'Salle Paris - Local LBDR'; // Lieu par défaut
    }

    /**
     * Retourne la date de création formatée pour l'affichage
     */
    public function getDateCreation(): string {
        $dateCreation = parent::getDateCreation();
        try {
            $date = new \DateTime($dateCreation);
            return $date->format('j F Y');
        } catch (\Exception $e) {
            return $dateCreation; // Retourne la date originale si le format échoue
        }
    }

    /**
     * Retourne le nombre de joueurs inscrits à la partie
     */
    public function getJoueursInscrits(): int {
        if (isset($this->id)) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM membres_partie WHERE id_partie = :id_partie');
            $stmt->execute(['id_partie' => $this->id]);
            return (int) $stmt->fetchColumn();
        }
        return 0;
    }

    /**
     * Retourne le nombre maximum de joueurs formaté pour l'affichage
     */
    public function getMaxJoueurs(): int {
        return $this->getNombreMaxJoueurs();
    }

    

    /**
     * Retourne l'image d'affichage
     */
    public function getImage(): ?Image {
        return $this->displayImage;
    }

    /**
     * Retourne l'URL de l'image d'affichage de la partie
     */
    public function getImageURL(): string {
        if ($this->displayImage) {
            return $this->displayImage->getFilePath();
        }
        return '/public/data/images/default-partie.png'; // Image par défaut
    }

    /**
     * Retourne le texte alternatif de l'image d'affichage
     */
    public function getImageALT(): string {
        if ($this->displayImage) {
            return $this->displayImage->getImageAlt();
        }
        return 'Image de la partie ' . $this->getNom();
    }

}
