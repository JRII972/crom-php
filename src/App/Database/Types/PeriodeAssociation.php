<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant une période d'association dans la base de données.
 */
class PeriodeAssociation extends DefaultDatabaseType
{
    private DateTime $dateDebut;
    private DateTime $dateFin;

    /**
     * Constructeur de la classe PeriodeAssociation.
     *
     * @param int|null $id Identifiant de la période (si fourni, charge depuis la base)
     * @param DateTime|null $dateDebut Date de début (requis si $id est null)
     * @param DateTime|null $dateFin Date de fin (requis si $id est null)
     * @throws InvalidArgumentException Si les paramètres sont incohérents ou invalides
     * @throws PDOException Si la période n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        ?DateTime $dateDebut = null,
        ?DateTime $dateFin = null
    ) {
        parent::__construct();
        $this->table = 'periodes_association';

        if ($id !== null) {
            // Mode : Charger la période depuis la base
            $this->loadFromDatabase($id);
        } elseif ($dateDebut !== null && $dateFin !== null) {
            // Mode : Créer une nouvelle période
            $this->setDateDebut($dateDebut);
            $this->setDateFin($dateFin);
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit dateDebut et dateFin.'
            );
        }
    }

    /**
     * Charge les données de la période depuis la base de données.
     *
     * @param int $id Identifiant de la période
     * @throws PDOException Si la période n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM periodes_association WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Période non trouvée pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->dateDebut = new DateTime($data['date_debut']);
        $this->dateFin = new DateTime($data['date_fin']);
    }

    /**
     * Sauvegarde la période dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE periodes_association SET
                    date_debut = :date_debut,
                    date_fin = :date_fin
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'date_debut' => $this->dateDebut->format('Y-m-d'),
                'date_fin' => $this->dateFin->format('Y-m-d'),
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO periodes_association (date_debut, date_fin)
                VALUES (:date_debut, :date_fin)
            ');
            $stmt->execute([
                'date_debut' => $this->dateDebut->format('Y-m-d'),
                'date_fin' => $this->dateFin->format('Y-m-d'),
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime la période de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer une période sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM periodes_association WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucune période supprimée : période non trouvée.');
        }

        return true;
    }

    /**
     * Recherche des périodes avec filtre optionnel par date de début ou date de fin.
     *
     * @param PDO $pdo Instance PDO
     * @param string $dateDebut Date de début (format Y-m-d, optionnel)
     * @param string $dateFin Date de fin (format Y-m-d, optionnel)
     * @return array Liste des périodes
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $dateDebut = '', string $dateFin = ''): array
    {
        $sql = 'SELECT id, date_debut, date_fin FROM periodes_association WHERE 1=1';
        $params = [];

        if ($dateDebut !== '') {
            $sql .= ' AND date_debut >= :date_debut';
            $params['date_debut'] = $dateDebut;
        }
        if ($dateFin !== '') {
            $sql .= ' AND date_fin <= :date_fin';
            $params['date_fin'] = $dateFin;
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

    public function getDateDebut(): DateTime
    {
        return $this->dateDebut;
    }

    public function getDateFin(): DateTime
    {
        return $this->dateFin;
    }

    // Setters

    public function setDateDebut(DateTime $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        // Vérifier que date_fin est postérieure ou égale si déjà définie
        if (isset($this->dateFin) && $this->dateFin < $dateDebut) {
            throw new InvalidArgumentException('La date de début doit être antérieure ou égale à la date de fin.');
        }
        return $this;
    }

    public function setDateFin(DateTime $dateFin): self
    {
        if ($this->dateDebut !== null && $dateFin < $this->dateDebut) {
            throw new InvalidArgumentException('La date de fin doit être postérieure ou égale à la date de début.');
        }
        $this->dateFin = $dateFin;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'date_debut' => $this->getDateDebut()->format('Y-m-d'),
            'date_fin' => $this->getDateFin()->format('Y-m-d'),
        ];
    }
}