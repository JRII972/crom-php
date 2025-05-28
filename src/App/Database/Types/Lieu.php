<?php

declare(strict_types=1);

namespace App\Database\Types;

use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant un lieu dans la base de données.
 */
class Lieu extends DefaultDatabaseType
{
    private string $nom;
    private string $short_nom;
    private ?string $adresse = null;
    private ?float $latitude = null;
    private ?float $longitude = null;
    private ?string $description = null;

    /**
     * Constructeur de la classe Lieu.
     *
     * @param int|null $id Identifiant du lieu (si fourni, charge depuis la base)
     * @param string|null $nom Nom du lieu (requis si $id est null)
     * @param string|null $short_nom Nom court du lieu (optionnel, généré par défaut)
     * @param string|null $adresse Adresse du lieu
     * @param float|null $latitude Latitude GPS
     * @param float|null $longitude Longitude GPS
     * @param string|null $description Description du lieu
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si le lieu n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $short_nom = null,
        ?string $adresse = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $description = null
    ) {
        parent::__construct();
        $this->table = 'lieux';

        if ($id !== null && $nom === null && $short_nom === null && $adresse === null && $latitude === null && $longitude === null && $description === null) {
            // Mode : Charger le lieu depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $nom !== null) {
            // Mode : Créer un nouveau lieu
            $this->setNom($nom);
            $this->setShortNom($short_nom); // Si null, generateShortNom sera appelé dans setNom
            if ($adresse !== null) {
                $this->setAdresse($adresse);
            }
            if ($latitude !== null) {
                $this->setLatitude($latitude);
            }
            if ($longitude !== null) {
                $this->setLongitude($longitude);
            }
            if ($description !== null) {
                $this->setDescription($description);
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit un nom (et éventuellement short_nom, adresse, latitude, longitude, description).'
            );
        }
    }

    /**
     * Génère un nom court à partir du nom complet du lieu.
     * Prend les initiales de chaque mot, max 5 caractères.
     *
     * @return string Le nom court généré
     */
    private function generateShortNom(): string
    {
        $words = preg_split('/\s+/', $this->nom);
        $initials = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper(mb_substr($word, 0, 1));
                if (strlen($initials) >= 5) {
                    break;
                }
            }
        }
        
        return substr($initials, 0, 5);
    }

    /**
     * Charge les données du lieu depuis la base de données.
     *
     * @param int $id Identifiant du lieu
     * @throws PDOException Si le lieu n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM lieux WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Lieu non trouvé pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->nom = $data['nom'];
        $this->short_nom = $data['short_nom'] ?? $this->generateShortNom();
        $this->adresse = $data['adresse'];
        $this->latitude = $data['latitude'] !== null ? (float) $data['latitude'] : null;
        $this->longitude = $data['longitude'] !== null ? (float) $data['longitude'] : null;
        $this->description = $data['description'];
    }

    /**
     * Sauvegarde le lieu dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE lieux SET
                    nom = :nom,
                    short_nom = :short_nom,
                    adresse = :adresse,
                    latitude = :latitude,
                    longitude = :longitude,
                    description = :description
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'nom' => $this->nom,
                'short_nom' => $this->short_nom,
                'adresse' => $this->adresse,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'description' => $this->description,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO lieux (nom, short_nom, adresse, latitude, longitude, description)
                VALUES (:nom, :short_nom, :adresse, :latitude, :longitude, :description)
            ');
            $stmt->execute([
                'nom' => $this->nom,
                'short_nom' => $this->short_nom,
                'adresse' => $this->adresse,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'description' => $this->description,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime le lieu de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un lieu sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM lieux WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * Recherche des lieux avec filtre optionnel par mot-clé.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé de recherche (optionnel)
     * @return array Liste des lieux
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $keyword = ''): array
    {
        $sql = 'SELECT id, nom, short_nom, adresse, latitude, longitude, description FROM lieux WHERE 1=1';
        $params = [];

        if ($keyword !== '') {
            // TODO: Améliorer les performances de la recherche (ex. ajouter un index sur nom et adresse)
            $sql .= ' AND (nom LIKE :keyword OR adresse LIKE :keyword OR short_nom LIKE :keyword)';
            $params['keyword'] = '%' . $keyword . '%';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getShortNom(): string
    {
        if (is_null($this->short_nom)){
            $this->short_nom = $this->generateShortNom();
        }
        return $this->short_nom;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Setters

    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom du lieu ne peut pas être vide.');
        }
        if (strlen($nom) > 255) {
            throw new InvalidArgumentException('Le nom du lieu ne peut pas dépasser 255 caractères.');
        }
        $this->nom = $nom;
        
        // Régénérer short_nom si pas déjà défini manuellement
        if (empty($this->short_nom)) {
            $this->short_nom = $this->generateShortNom();
        }
        
        return $this;
    }

    public function setShortNom(?string $short_nom): self
    {
        if ($short_nom !== null) {
            if (strlen($short_nom) > 5) {
                throw new InvalidArgumentException('Le nom court ne peut pas dépasser 5 caractères.');
            }
            $this->short_nom = strtoupper($short_nom);
        } else if (isset($this->nom)) {
            $this->short_nom = $this->generateShortNom();
        }
        return $this;
    }

    public function setAdresse(?string $adresse): self
    {
        if ($adresse !== null && strlen($adresse) > 255) {
            throw new InvalidArgumentException('L\'adresse ne peut pas dépasser 255 caractères.');
        }
        $this->adresse = $adresse;
        return $this;
    }

    public function setLatitude(?float $latitude): self
    {
        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            throw new InvalidArgumentException('La latitude doit être comprise entre -90 et 90.');
        }
        $this->latitude = $latitude;
        return $this;
    }

    public function setLongitude(?float $longitude): self
    {
        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            throw new InvalidArgumentException('La longitude doit être comprise entre -180 et 180.');
        }
        $this->longitude = $longitude;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        if ($description !== null && strlen($description) > 65535) {
            throw new InvalidArgumentException('La description du lieu est trop longue.');
        }
        $this->description = $description;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'short_nom' => $this->getShortNom(),
            'adresse' => $this->getAdresse(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'description' => $this->getDescription(),
        ];
    }
}