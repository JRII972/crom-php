<?php

namespace App\Controllers\Class;

use App\Database\Types\Utilisateur;
use App\Database\Types\Sexe;
use App\Database\Types\TypeUtilisateur;
use App\Utils\Image;
use DateTime;
use InvalidArgumentException;
use PDO;

class UtilisateurDisplay extends Utilisateur 
{
    private ?Image $displayImage = null;

    /**
     * Constructeur de la classe UtilisateurDisplay.
     * Appelle le constructeur parent pour initialiser l'utilisateur.
     */
    public function __construct(
        ?string $id = null,
        ?string $prenom = null,
        ?string $nom = null,
        ?string $nomUtilisateur = null,
        ?string $motDePasse = null,
        ?Sexe $sexe = null,
        ?string $email = null,
        ?DateTime $dateDeNaissance = null,
        ?string $idDiscord = null,
        ?string $pseudonyme = null,
        Image|string|array|null $image = null,
        ?TypeUtilisateur $typeUtilisateur = null,
        ?DateTime $dateInscription = null,
        ?bool $ancienUtilisateur = null,
        ?bool $premiereConnexion = null
    ) {
        parent::__construct(
            $id,
            $prenom,
            $nom,
            $nomUtilisateur,
            $motDePasse,
            $sexe,
            $email,
            $dateDeNaissance,
            $idDiscord,
            $pseudonyme,
            $image,
            $typeUtilisateur,
            $dateInscription,
            $ancienUtilisateur,
            $premiereConnexion
        );

        $avatar = parent::getImage();
        if ($avatar !== null) {
            $this->displayImage = $avatar;
        } else {
            if ($this->displayImage === null) {
                $this->displayImage = new Image('/data/images/default/default_jdr_image_1.webp');
        }
        }
    }

    /**
     * Recherche des utilisateurs avec les mêmes paramètres que la classe parent
     */
    public static function search(PDO $pdo, string $email = '', string $nomUtilisateur = '', string $typeUtilisateur = ''): array {
        $utilisateurs = parent::search($pdo, $email, $nomUtilisateur, $typeUtilisateur);
        
        // Convertir chaque Utilisateur en UtilisateurDisplay
        $utilisateurDisplays = [];
        foreach ($utilisateurs as $utilisateur) {
            $utilisateurDisplays[] = new self($utilisateur->getId());
        }
        
        return $utilisateurDisplays;
    }

    /**
     * Retourne le nom d'affichage de l'utilisateur.
     * Utilise le pseudonyme si disponible, sinon la première lettre du nom + le prénom.
     *
     * @return string Le nom d'affichage
     */
    public function displayName(): string {
        // Si un pseudonyme est défini, l'utiliser
        if ($this->getPseudonyme() !== null && !empty(trim($this->getPseudonyme()))) {
            return $this->getPseudonyme();
        }
        
        // Sinon, utiliser la première lettre du nom + le prénom
        $premiereLettre = substr($this->getNom(), 0, 1);
        return $premiereLettre . '. ' . $this->getPrenom();
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
}
