<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant une notification Helloasso dans la base de données.
 */
class NotificationsHelloasso extends DefaultDatabaseType
{
    private string $typeEvenement;
    private string $dateEvenement;
    private string $donnees;
    private string $dateReception;
    private bool $traite;

    /**
     * Constructeur de la classe NotificationsHelloasso.
     *
     * @param string|null $id Identifiant de la notification (si fourni, charge depuis la base)
     * @param string|null $typeEvenement Type d'événement (requis si $id est null)
     * @param string|null $dateEvenement Date de l'événement (format Y-m-d H:i:s, requis si $id est null)
     * @param string|null $donnees Données JSON (requis si $id est null)
     * @param string|null $dateReception Date de réception (format Y-m-d H:i:s, optionnel, défaut CURRENT_TIMESTAMP)
     * @param bool|null $traite Statut de traitement (optionnel, défaut FALSE)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si la notification n'existe pas dans la base
     */
    public function __construct(
        ?string $id = null,
        ?string $typeEvenement = null,
        ?string $dateEvenement = null,
        ?string $donnees = null,
        ?string $dateReception = null,
        ?bool $traite = null
    ) {
        parent::__construct();
        $this->table = 'notifications_helloasso';

        if ($id !== null && $typeEvenement === null && $dateEvenement === null && $donnees === null && 
            $dateReception === null && $traite === null) {
            // Mode : Charger la notification depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $typeEvenement !== null && $dateEvenement !== null && $donnees !== null) {
            // Mode : Créer une nouvelle notification
            $this->setId(generateUUID());
            $this->setTypeEvenement($typeEvenement);
            $this->setDateEvenement($dateEvenement);
            $this->setDonnees($donnees);
            $this->setDateReception($dateReception ?? date('Y-m-d H:i:s'));
            $this->setTraite($traite ?? false);
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit typeEvenement, dateEvenement, et donnees ' .
                '(et éventuellement dateReception, traite).'
            );
        }
    }

    /**
     * Charge les données de la notification depuis la base de données.
     *
     * @param string $id Identifiant de la notification
     * @throws PDOException Si la notification n'existe pas
     */
    private function loadFromDatabase(string $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notifications_helloasso WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Notification non trouvée pour l\'ID : ' . $id);
        }

        $this->id = $data['id'];
        $this->typeEvenement = $data['type_evenement'];
        $this->dateEvenement = $data['date_evenement'];
        $this->donnees = $data['donnees'];
        $this->dateReception = $data['date_reception'];
        $this->traite = (bool) $data['traite'];
    }

    /**
     * Sauvegarde la notification dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        // Vérifier si la notification existe déjà
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notifications_helloasso WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE notifications_helloasso SET
                    type_evenement = :type_evenement,
                    date_evenement = :date_evenement,
                    donnees = :donnees,
                    date_reception = :date_reception,
                    traite = :traite
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'type_evenement' => $this->typeEvenement,
                'date_evenement' => $this->dateEvenement,
                'donnees' => $this->donnees,
                'date_reception' => $this->dateReception,
                'traite' => (int) $this->traite,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO notifications_helloasso (
                    id, type_evenement, date_evenement, donnees, date_reception, traite
                )
                VALUES (
                    :id, :type_evenement, :date_evenement, :donnees, :date_reception, :traite
                )
            ');
            $stmt->execute([
                'id' => $this->id,
                'type_evenement' => $this->typeEvenement,
                'date_evenement' => $this->dateEvenement,
                'donnees' => $this->donnees,
                'date_reception' => $this->dateReception,
                'traite' => (int) $this->traite,
            ]);
        }
    }

    /**
     * Supprime la notification de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer une notification sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM notifications_helloasso WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucune notification supprimée : notification non trouvée.');
        }
        return true;
    }

    /**
     * Recherche des notifications avec filtre optionnel par type d'événement ou statut traité.
     *
     * @param PDO $pdo Instance PDO
     * @param string $typeEvenement Type d'événement (optionnel)
     * @param bool|null $traite Statut de traitement (optionnel, true/false)
     * @return array Liste des notifications
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $typeEvenement = '', ?bool $traite = null): array
    {
        $sql = 'SELECT id, type_evenement, date_evenement, donnees, date_reception, traite FROM notifications_helloasso WHERE 1=1';
        $params = [];

        if ($typeEvenement !== '') {
            $sql .= ' AND type_evenement = :type_evenement';
            $params['type_evenement'] = $typeEvenement;
        }
        if ($traite !== null) {
            $sql .= ' AND traite = :traite';
            $params['traite'] = (int) $traite;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters

    public function getId(): string
    {
        return $this->id;
    }

    public function getTypeEvenement(): string
    {
        return $this->typeEvenement;
    }

    public function getDateEvenement(): string
    {
        return $this->dateEvenement;
    }

    public function getDonnees(): string
    {
        return $this->donnees;
    }

    public function getDateReception(): string
    {
        return $this->dateReception;
    }

    public function getTraite(): bool
    {
        return $this->traite;
    }

    // Setters

    public function setId(string $id): self
    {
        if (empty(trim($id))) {
            throw new InvalidArgumentException('L\'identifiant ne peut pas être vide.');
        }
        $this->id = $id;
        return $this;
    }

    public function setTypeEvenement(string $typeEvenement): self
    {
        if (empty(trim($typeEvenement))) {
            throw new InvalidArgumentException('Le type d\'événement ne peut pas être vide.');
        }
        if (strlen($typeEvenement) > 100) {
            throw new InvalidArgumentException('Le type d\'événement ne peut pas dépasser 100 caractères.');
        }
        $this->typeEvenement = $typeEvenement;
        return $this;
    }

    public function setDateEvenement(string $dateEvenement): self
    {
        if (!isValidDateTime($dateEvenement)) {
            throw new InvalidArgumentException('La date de l\'événement doit être au format Y-m-d H:i:s.');
        }
        $this->dateEvenement = $dateEvenement;
        return $this;
    }

    public function setDonnees(string $donnees): self
    {
        if (!isValidJson($donnees)) {
            throw new InvalidArgumentException('Les données doivent être un JSON valide.');
        }
        $this->donnees = $donnees;
        return $this;
    }

    public function setDateReception(string $dateReception): self
    {
        if (!isValidDateTime($dateReception)) {
            throw new InvalidArgumentException('La date de réception doit être au format Y-m-d H:i:s.');
        }
        $this->dateReception = $dateReception;
        return $this;
    }

    public function setTraite(bool $traite): self
    {
        $this->traite = $traite;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'type_evenement' => $this->getTypeEvenement(),
            'date_evenement' => $this->getDateEvenement(),
            'donnees' => $this->getDonnees(),
            'date_reception' => $this->getDateReception(),
            'traite' => $this->getTraite(),
        ];
    }
}