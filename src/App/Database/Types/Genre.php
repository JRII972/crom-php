<?php

declare(strict_types=1);

namespace App\Database\Types;

use InvalidArgumentException;
use PDO;
use PDOException;

class Genre extends DefaultDatabaseType
{
    private string $nom;

    /**
     * Constructeur de la classe Genre.
     *
     * @param int|null $id Identifiant du genre (si fourni, charge depuis la base)
     * @param string|null $nom Nom du genre (requis si $id est null)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si le genre n'existe pas dans la base
     */
    public function __construct(?int $id = null, ?string $nom = null)
    {
        parent::__construct();
        $this->table = 'genres';

        if ($id !== null && $nom === null) {
            // Mode : Charger le genre depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $nom !== null) {
            // Mode : Créer un nouveau genre
            $this->setNom($nom);
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit un nom seul.'
            );
        }
    }

    /**
     * Charge les données du genre depuis la base de données.
     *
     * @param int $id Identifiant du genre
     * @throws PDOException Si le genre n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM genres WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Genre non trouvé pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->nom = $data['nom'];
    }

    /**
     * Sauvegarde le genre dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL (ex. violation d'unicité sur nom)
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('UPDATE genres SET nom = :nom WHERE id = :id');
            $stmt->execute([
                'id' => $this->id,
                'nom' => $this->nom,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('INSERT INTO genres (nom) VALUES (:nom)');
            $stmt->execute(['nom' => $this->nom]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime le genre de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un genre sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM genres WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * Recherche des genres par mot-clé.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé de recherche (optionnel)
     * @return array Liste des genres
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $keyword = ''): array
    {
        $sql = 'SELECT * FROM genres';
        $params = [];
        if (!empty($keyword)) {
            $sql .= ' WHERE nom LIKE :keyword';
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

    // Setters

    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom du genre ne peut pas être vide.');
        }
        $this->nom = $nom;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
        ];
    }
}