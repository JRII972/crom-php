<?php

namespace App\Controllers\Class;

use App\Database\Types\Activite;
use App\Database\Types\Jeu;
use App\Database\Types\Utilisateur;
use App\Utils\Image;
use Illuminate\Support\Arr;
use PDO;
use PDOException;

class ActiviteDisplay extends Activite 
{
    private ?Image $displayImage = null;

    /**
     * Constructeur de la classe ActiviteDisplay.
     * Gère automatiquement les erreurs de activite inexistante.
     *
     * @param int|null $id Identifiant de la activite
     * @param Jeu|int|null $jeuOuId Objet Jeu ou ID du jeu
     * @param string|null $nom Nom de la activite
     * @param string|null $description Description de la activite
     * @param Image|string|array|null $image Image de la activite
     * @param \App\Database\Types\TypeActivite|null $typeActivite Type de activite
     * @param \App\Database\Types\TypeCampagne|null $typeCampagne Type de campagne
     * @param Utilisateur|string|null $maitreJeuOuId Maître du jeu
     * @param int|null $maxJoueurs Nombre maximum de joueurs
     * @param bool|null $estPublique Si la activite est publique
     * @param bool|null $estActive Si la activite est active
     * @throws PDOException Si la activite n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Jeu|int|null $jeuOuId = null,
        ?string $nom = null,
        ?string $description = null,
        Image|string|array|null $image = null,
        ?\App\Database\Types\TypeActivite $typeActivite = null,
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
            $typeActivite,
            $typeCampagne,
            $maitreJeuOuId,
            $maxJoueurs,
            $estPublique,
            $estActive
        );

        // Initialiser l'image d'affichage avec fallback
        $activiteImage = parent::getImage();
        if ($activiteImage !== null) {
            $this->displayImage = $activiteImage;
        } else {
            // Fallback vers l'image du jeu
            $jeu = $this->getJeu();
            if ($jeu && $jeu->getImage()) {
                $this->displayImage = $jeu->getImage();
            }
        }
    }

    /**
     * Méthode statique pour créer une ActiviteDisplay de manière sécurisée.
     * Retourne null si la activite n'existe pas au lieu de lever une exception.
     *
     * @param int|null $id Identifiant de la activite
     * @return ActiviteDisplay|null La activite ou null si elle n'existe pas
     */
    public static function createSafe(int|null $id): ?ActiviteDisplay
    {
        if ( is_null($id)) { 
            return null;
        }
        
        try {
            return new self($id);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Vérifie si une activite existe dans la base de données.
     *
     * @param PDO $pdo Instance PDO
     * @param int $id Identifiant de la activite
     * @return bool True si la activite existe, false sinon
     */
    public static function exists(PDO $pdo, int $id): bool
    {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM activite WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Recherche des activites avec filtres optionnels.
     * Compatible avec la signature de la classe parent.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé pour la recherche (optionnel)
     * @param int $idJeu ID du jeu (optionnel)
     * @param string $idMaitreJeu ID du maître du jeu (optionnel)
     * @param string $typeActivite Type de activite (optionnel)
     * @param array|null $categories Liste des catégories/genres pour filtrer (optionnel)
     * @param array|null $jours Liste des jours de la semaine pour filtrer (optionnel)
     * @param string|array $etats État(s) de l'activité pour filtrer (optionnel, ACTIVE, FERMER, TERMINER, ANNULER, SUPPRIMER, BROUILLON)
     * @return array Liste des activites
     */
    public static function search(
        PDO $pdo,
        string $keyword = '',
        int $idJeu = 0,
        string $idMaitreJeu = '',
        string $typeActivite = '',
        ?array $categories = null,
        ?array $jours = null,
        string|array $etats = ''
    ): array {
        $activites = parent::search(
            $pdo,
            $keyword,
            $idJeu,
            $idMaitreJeu,
            $typeActivite,
            $categories,
            $jours,
            $etats
        );
        
        $activiteDisplays = [];
        foreach ($activites as $activite) {
            $activiteDisplays[] = new self($activite['id']);
        }
        
        return $activiteDisplays;
    }

    /**
     * Retourne le maître du jeu 
     */
    public function getMaitreJeu(): ?UtilisateurDisplay {
        return new UtilisateurDisplay($this->idMaitreJeu);
    }

    /**
     * Retourne le type de activite formaté pour l'affichage
     */
    public function getTypeFormatted(): string {
        $typeActivite = $this->getTypeActivite();
        
        $typeDisplay = match($typeActivite->value) {
            'CAMPAGNE' => 'Campagne',
            'ONESHOT' => 'One-Shot',
            'JEU_DE_SOCIETE' => 'Jeu de Société',
            'EVENEMENT' => 'Événement',
            default => $typeActivite->value
        };
        
        // Si c'est une campagne, ajouter le type de campagne
        if ($typeActivite->value === 'CAMPAGNE' && $this->getTypeCampagne()) {
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
     * Retourne le type de activite formaté en version courte
     */
    public function getTypeFormattedShort(): string {
        $typeActivite = $this->getTypeActivite();
        
        $typeDisplay = match($typeActivite->value) {
            'CAMPAGNE' => 'Cmp',
            'ONESHOT' => '1Sht',
            'JEU_DE_SOCIETE' => 'JdS',
            'EVENEMENT' => 'Event',
            default => $typeActivite->value
        };
        
        // Si c'est une campagne, ajouter le type de campagne
        if ($typeActivite->value === 'CAMPAGNE' && $this->getTypeCampagne()) {
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
     * Retourne le jeu sous forme de JeuDisplay
     */
    public function getJeu(): JeuDisplay {
        return new JeuDisplay($this->idJeu);
    }

    /**
     * Retourne le lieu par défaut pour l'affichage de la activite.
     * Si la activite a des sessions, retourne le lieu de la première session.
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
     * Retourne le nombre de joueurs inscrits à la activite
     */
    public function getJoueursInscrits(): array {
        $membres = [];

        $stmt = $this->pdo->prepare('SELECT id_utilisateur FROM membres_activite WHERE id_activite = :id_activite');
        $stmt->execute(['id_activite' => $this->id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            try {
                $utilisateur = new UtilisateurDisplay($result['id_utilisateur']);
                $membres[] = $utilisateur; // À ajuster si MembreActivite est requis
            } catch (PDOException) {
                // Ignorer les utilisateurs non trouvés
            }
        }

        return $membres;
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
     * Retourne l'URL de l'image d'affichage de la activite
     */
    public function getImageURL(): string {
        if ($this->displayImage) {
            return $this->displayImage->getFilePath();
        }
        return '/public/data/images/default-activite.png'; // Image par défaut
    }

    /**
     * Retourne le texte alternatif de l'image d'affichage
     */
    public function getImageALT(): string {
        if ($this->displayImage) {
            return $this->displayImage->getImageAlt();
        }
        return 'Image de la activite ' . $this->getNom();
    }

    public function getSessions(): array {
        
        try {
            $sessions = [];

            $stmt = $this->pdo->prepare('SELECT id FROM sessions WHERE id_activite = :id_activite ORDER BY date_session ASC');
            $stmt->execute(['id_activite' => $this->id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $result) {
                try {
                    $sessions[] = new SessionDisplay($result['id']);
                } catch (PDOException) {
                    // Ignorer les sessions non trouvées
                }
            }

            return $sessions;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getNextSessions(): array {
        try {
            $sessions = [];

            $stmt = $this->pdo->prepare('SELECT id FROM sessions WHERE id_activite = :id_activite AND date_session >= CURDATE() ORDER BY date_session ASC');
            $stmt->execute(['id_activite' => $this->id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $result) {
                try {
                    $sessions[] = new SessionDisplay($result['id']);
                } catch (PDOException) {
                    // Ignorer les sessions non trouvées
                }
            }

            return $sessions;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getStatutFormatted() : string {
        return $this->getVerrouille() ? "Fermer à l'inscription" : "Ouverte à l'inscription";
    }



}
