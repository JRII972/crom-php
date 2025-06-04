<?php

namespace App\Controllers\Class;

use App\Database\Types\Jeu;
use App\Utils\Image;
use PDO;

class JeuDisplay extends Jeu 
{
    private Image|null $displayImage;

    /**
     * Constructeur de la classe JeuDisplay.
     * Appelle le constructeur parent puis initialise l'image d'affichage avec un fallback.
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $description = null,
        Image|string|array|null $image = null,
        Image|string|array|null $icon = null,
        ?\App\Database\Types\TypeJeu $typeJeu = null
    ) {
        parent::__construct($id, $nom, $description, $image, $icon, $typeJeu);
        
        // Initialiser l'image d'affichage avec fallback
        $this->displayImage = parent::getImage();
        if ($this->displayImage === null) {
            $this->displayImage = new Image(
                '/data/images/default/default_jdr_image_1.webp',
                'default_jeu_' . $this->getId(),
                '/Jeux',
                'Image par défaut pour ' . $this->getNom(),
                false
            );
        }
    }

    /**
     * Recherche des jeux avec les mêmes paramètres que la classe parent
     */
    public static function search(
        PDO $pdo, 
        string $keyword = '', 
        string $typeJeu = '', 
        string $genres = ''
    ): array {
        return parent::search($pdo, $keyword, $typeJeu, $genres);
    }

    public function getImageURL(): string {
        return $this->displayImage->getFilePath();
    }

    public function getImageALT(): string {
        return $this->displayImage->getImageAlt();
    }

    public function getImage(): Image {
        return $this->displayImage;
    }
}