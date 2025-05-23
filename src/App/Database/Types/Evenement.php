<?php

declare(strict_types=1);

namespace App\Database\Types;

use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant un événement dans la base de données.
 */
class Evenement extends DefaultDatabaseType
{
    private string $nom;
    private ?string $description = null;
    private string $dateDebut;
    private string $dateFin;
    private ?int $idLieu = null;
    private ?Lieu $lieu = null;
    private ?string $regleRecurrence = null;
    private ?string $exceptions = null;
    private string $dateCreation;

    /**
     * Constructeur de la classe Evenement.
     *
     * @param int|null $id Identifiant de l'événement (si fourni, charge depuis la base)
     * @param string|null $nom Nom de l'événement (requis si $id est null)
     * @param string|null $description Description de l'événement
     * @param string|null $dateDebut Date de début (format Y-m-d)
     * @param string|null $dateFin Date de fin (format Y-m-d)
     * @param int|Lieu|null $idLieu Identifiant ou objet Lieu
     * @param string|null $regleRecurrence Règle de récurrence (JSON)
     * @param string|null $exceptions Exceptions (JSON)
     * @param string|null $dateCreation Date de création (format Y-m-d H:i:s)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si l'événement n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $description = null,
        ?string $dateDebut = null,
        ?string $dateFin = null,
        int|Lieu|null $idLieu = null,
        ?string $regleRecurrence = null,
        ?string $exceptions = null,
        ?string $dateCreation = null
    ) {
        parent::__construct();
        $this->table = 'evenements';

        if ($id !== null && $nom === null && $description === null && $dateDebut === null && 
            $dateFin === null && $idLieu === null && $regleRecurrence === null && 
            $exceptions === null && $dateCreation === null) {
            // Mode : Charger l'événement depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $nom !== null && $dateDebut !== null && $dateFin !== null) {
            // Mode : Créer un nouvel événement
            $this->setNom($nom);
            $this->setDateDebut($dateDebut);
            $this->setDateFin($dateFin);
            if ($description !== null) {
                $this->setDescription($description);
            }
            if ($idLieu !== null) {
                $this->setLieu($idLieu);
            }
            if ($regleRecurrence !== null) {
                $this->setRegleRecurrence($regleRecurrence);
            }
            if ($exceptions !== null) {
                $this->setExceptions($exceptions);
            }
            if ($dateCreation !== null) {
                $this->setDateCreation($dateCreation);
            } else {
                $this->dateCreation = date('Y-m-d H:i:s');
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit un nom, dateDebut et dateFin ' .
                '(et éventuellement description, idLieu, regleRecurrence, exceptions, dateCreation).'
            );
        }
    }

    /**
     * Charge les données de l'événement depuis la base de données.
     *
     * @param int $id Identifiant de l'événement
     * @throws PDOException Si l'événement n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM evenements WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Événement non trouvé pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->nom = $data['nom'];
        $this->description = $data['description'];
        $this->dateDebut = $data['date_debut'];
        $this->dateFin = $data['date_fin'];
        $this->idLieu = $data['id_lieu'] !== null ? (int) $data['id_lieu'] : null;
        $this->regleRecurrence = $data['regle_recurrence'];
        $this->exceptions = $data['exceptions'];
        $this->dateCreation = $data['date_creation'];
    }

    /**
     * Sauvegarde l'événement dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE evenements SET
                    nom = :nom,
                    description = :description,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    id_lieu = :id_lieu,
                    regle_recurrence = :regle_recurrence,
                    exceptions = :exceptions,
                    date_creation = :date_creation
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'nom' => $this->nom,
                'description' => $this->description,
                'date_debut' => $this->dateDebut,
                'date_fin' => $this->dateFin,
                'id_lieu' => $this->idLieu,
                'regle_recurrence' => $this->regleRecurrence,
                'exceptions' => $this->exceptions,
                'date_creation' => $this->dateCreation,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO evenements (
                    nom, description, date_debut, date_fin, id_lieu, 
                    regle_recurrence, exceptions, date_creation
                )
                VALUES (
                    :nom, :description, :date_debut, :date_fin, :id_lieu, 
                    :regle_recurrence, :exceptions, :date_creation
                )
            ');
            $stmt->execute([
                'nom' => $this->nom,
                'description' => $this->description,
                'date_debut' => $this->dateDebut,
                'date_fin' => $this->dateFin,
                'id_lieu' => $this->idLieu,
                'regle_recurrence' => $this->regleRecurrence,
                'exceptions' => $this->exceptions,
                'date_creation' => $this->dateCreation,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime l'événement de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un événement sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM evenements WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * Recherche des événements avec filtre optionnel par nom ou date de début.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Nom ou date de début (format Y-m-d, optionnel)
     * @return array Liste des événements
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $keyword = ''): array
    {
        $sql = 'SELECT id, nom, description, date_debut, date_fin, id_lieu, regle_recurrence, exceptions, date_creation FROM evenements WHERE 1=1';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' AND (nom LIKE :keyword OR date_debut = :keyword)';
            $params['keyword'] = $keyword;
            if (!$keyword) {
                $params['keyword'] = '%' . $keyword . '%';
            }
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDateDebut(): string
    {
        return $this->dateDebut;
    }

    public function getDateFin(): string
    {
        return $this->dateFin;
    }

    public function getIdLieu(): ?int
    {
        return $this->idLieu;
    }

    public function getLieu(): ?Lieu
    {
        if ($this->lieu === null && $this->idLieu !== null) {
            try {
                $this->lieu = new Lieu($this->idLieu);
            } catch (PDOException) {
                // Lieu non trouvé, retourner null
                $this->idLieu = null;
            }
        }
        return $this->lieu;
    }

    public function getRegleRecurrence(): ?string
    {
        return $this->regleRecurrence;
    }

    public function getExceptions(): ?string
    {
        return $this->exceptions;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    // Setters

    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom de l\'événement ne peut pas être vide.');
        }
        if (strlen($nom) > 255) {
            throw new InvalidArgumentException('Le nom de l\'événement ne peut pas dépasser 255 caractères.');
        }
        $this->nom = $nom;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setDateDebut(string $dateDebut): self
    {
        if (!isValidDate($dateDebut)) {
            throw new InvalidArgumentException('La date de début doit être au format Y-m-d.');
        }
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function setDateFin(string $dateFin): self
    {
        if (!isValidDate($dateFin)) {
            throw new InvalidArgumentException('La date de fin doit être au format Y-m-d.');
        }
        if ($this->dateDebut && strtotime($dateFin) < strtotime($this->dateDebut)) {
            throw new InvalidArgumentException('La date de fin ne peut pas être antérieure à la date de début.');
        }
        $this->dateFin = $dateFin;
        return $this;
    }

    public function setLieu(int|Lieu|null $lieu): self
    {
        if ($lieu instanceof Lieu) {
            $this->lieu = $lieu;
            $this->idLieu = $lieu->getId();
        } elseif (is_int($lieu)) {
            $this->idLieu = $lieu;
            $this->lieu = null; // Lazy loading lors de getLieu()
        } else {
            $this->idLieu = null;
            $this->lieu = null;
        }
        return $this;
    }

    public function setRegleRecurrence(?string $regleRecurrence): self
    {
        if ($regleRecurrence !== null && !isValidJson($regleRecurrence)) {
            throw new InvalidArgumentException('La règle de récurrence doit être un JSON valide.');
        }
        $this->regleRecurrence = $regleRecurrence;
        return $this;
    }

    public function setExceptions(?string $exceptions): self
    {
        if ($exceptions !== null && !isValidJson($exceptions)) {
            throw new InvalidArgumentException('Les exceptions doivent être un JSON valide.');
        }
        $this->exceptions = $exceptions;
        return $this;
    }

    public function setDateCreation(string $dateCreation): self
    {
        if (!isValidDateTime($dateCreation)) {
            throw new InvalidArgumentException('La date de création doit être au format Y-m-d H:i:s.');
        }
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'description' => $this->getDescription(),
            'date_debut' => $this->getDateDebut(),
            'date_fin' => $this->getDateFin(),
            'id_lieu' => $this->getIdLieu(),
            'regle_recurrence' => $this->getRegleRecurrence(),
            'exceptions' => $this->getExceptions(),
            'date_creation' => $this->getDateCreation(),
        ];
    }
}