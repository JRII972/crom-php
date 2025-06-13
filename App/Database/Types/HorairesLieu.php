<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

require_once __DIR__ . '/../../Utils/helpers.php';

/**
 * Classe représentant un horaire de lieu dans la base de données.
 */
class HorairesLieu extends DefaultDatabaseType
{
    private ?int $idLieu;
    private ?Lieu $lieu = null;
    private ?int $numJour;
    private string $heureDebut;
    private string $heureFin;
    private ?string $dateDebut = null;
    private ?string $dateFin = null;
    private bool $exceptionnelle = false;
    private bool $isClosed = false;

    /**
     * Constructeur de la classe HorairesLieu.
     *
     * @param int|null $id Identifiant de l'horaire (si fourni, charge depuis la base)
     * @param Lieu|int|null $lieuOuId Objet Lieu ou ID du lieu (requis si $id est null)
     * @param int|null $numJour Numéro du jour (0-6, optionnel)
     * @param string|null $heureDebut Heure de début (format H:i:s, requis si $id est null)
     * @param string|null $heureFin Heure de fin (format H:i:s, requis si $id est null)
     * @param string|null $dateDebut Date de début (format Y-m-d, optionnel)
     * @param string|null $dateFin Date de fin (format Y-m-d, optionnel)
     * @param bool|null $exceptionnelle Indique si c'est un horaire exceptionnel (optionnel, défaut false)
     * @param bool|null $isClosed Indique si le lieu est fermé (optionnel, défaut false)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si l'horaire n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Lieu|int|null $lieuOuId = null,
        ?int $numJour = null,
        ?string $heureDebut = null,
        ?string $heureFin = null,
        ?string $dateDebut = null,
        ?string $dateFin = null,
        ?bool $exceptionnelle = false,
        ?bool $isClosed = false
    ) {
        parent::__construct();
        $this->table = 'horaires_lieu';

        if ($id !== null) {
            // Mode : Charger l'horaire depuis la base
            $this->loadFromDatabase($id);
        } elseif ($lieuOuId !== null && $heureDebut !== null && $heureFin !== null) {
            // Mode : Créer un nouvel horaire
            $this->setLieu($lieuOuId);
            $this->setHeureDebut($heureDebut);
            $this->setHeureFin($heureFin);
            
            if ($numJour !== null) {
                $this->setNumJour($numJour);
            }
            if ($dateDebut !== null) {
                $this->setDateDebut($dateDebut);
            }
            if ($dateFin !== null) {
                $this->setDateFin($dateFin);
            }
            if ($exceptionnelle !== null) {
                $this->setExceptionnelle($exceptionnelle);
            }
            if ($isClosed !== null) {
                $this->setIsClosed($isClosed);
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID, soit lieuOuId, heureDebut et heureFin.'
            );
        }
    }

    /**
     * Charge les données de l'horaire depuis la base de données.
     *
     * @param int $id Identifiant de l'horaire
     * @throws PDOException Si l'horaire n'existe pas
     */
    private function loadFromDatabase(int|null $id): void
    {
        if ($id === null) {
            $id = $this->id;
        }
        $stmt = $this->pdo->prepare('SELECT * FROM horaires_lieu WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Horaire non trouvé pour l\'ID : ' . $id);
        }

        $this->updateFromDatabaseData($data);
    }

    private function updateFromDatabaseData(array $data): void
    {
        $this->id = (int) $data['id'];
        $this->idLieu = (int) $data['id_lieu'];
        $this->numJour = $data['num_jour'] !== null ? (int) $data['num_jour'] : null;
        $this->heureDebut = $data['heure_debut'];
        $this->heureFin = $data['heure_fin'];
        $this->dateDebut = $data['date_debut'];
        $this->dateFin = $data['date_fin'];
        $this->exceptionnelle = (bool) $data['exceptionnelle'];
        $this->isClosed = (bool) $data['is_closed'];
    }

    /**
     * Sauvegarde l'horaire dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE horaires_lieu SET
                    id_lieu = :id_lieu,
                    num_jour = :num_jour,
                    heure_debut = :heure_debut,
                    heure_fin = :heure_fin,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    exceptionnelle = :exceptionnelle,
                    is_closed = :is_closed
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_lieu' => $this->idLieu,
                'num_jour' => $this->numJour,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'date_debut' => $this->dateDebut,
                'date_fin' => $this->dateFin,
                'exceptionnelle' => $this->exceptionnelle,
                'is_closed' => $this->isClosed,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO horaires_lieu (
                    id_lieu, num_jour, heure_debut, heure_fin, date_debut, date_fin, exceptionnelle, is_closed
                )
                VALUES (
                    :id_lieu, :num_jour, :heure_debut, :heure_fin, :date_debut, :date_fin, :exceptionnelle, :is_closed
                )
            ');
            $stmt->execute([
                'id_lieu' => $this->idLieu,
                'num_jour' => $this->numJour,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'date_debut' => $this->dateDebut,
                'date_fin' => $this->dateFin,
                'exceptionnelle' => $this->exceptionnelle,
                'is_closed' => $this->isClosed,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
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
                $this->idLieu = null;
            }
        }
        return $this->lieu;
    }

    public function getNumJour(): ?int
    {
        return $this->numJour;
    }

    public function getHeureDebut(): string
    {
        return $this->heureDebut;
    }

    public function getHeureFin(): string
    {
        return $this->heureFin;
    }

    public function getDateDebut(): ?string
    {
        return $this->dateDebut;
    }

    public function getDateFin(): ?string
    {
        return $this->dateFin;
    }

    public function getExceptionnelle(): bool
    {
        return $this->exceptionnelle;
    }

    public function getIsClosed(): bool
    {
        return $this->isClosed;
    }

    // Setters

    public function setLieu(Lieu|int $lieu): self
    {
        if ($lieu instanceof Lieu) {
            $this->lieu = $lieu;
            $this->idLieu = $lieu->getId();
        } else {
            $this->idLieu = $lieu;
            $this->lieu = null; // Lazy loading
        }
        return $this;
    }

    public function setNumJour(?int $numJour): self
    {
        if ($numJour !== null && ($numJour < 0 || $numJour > 6)) {
            throw new InvalidArgumentException('Le numéro de jour doit être entre 0 et 6.');
        }
        $this->numJour = $numJour;
        return $this;
    }

    public function setHeureDebut(string $heureDebut): self
    {
        if (!isValidTime($heureDebut)) {
            throw new InvalidArgumentException('L\'heure de début doit être au format H:i:s.');
        }
        $this->heureDebut = $heureDebut;
        return $this;
    }

    public function setHeureFin(string $heureFin): self
    {
        if (!isValidTime($heureFin)) {
            throw new InvalidArgumentException('L\'heure de fin doit être au format H:i:s.');
        }
        if (isset($this->heureDebut)) {
            $debut = DateTime::createFromFormat('H:i:s', $this->heureDebut);
            $fin = DateTime::createFromFormat('H:i:s', $heureFin);
            if ($fin <= $debut) {
                throw new InvalidArgumentException('L\'heure de fin doit être postérieure à l\'heure de début.');
            }
        }
        $this->heureFin = $heureFin;
        return $this;
    }

    public function setDateDebut(?string $dateDebut): self
    {
        if ($dateDebut !== null && !isValidDate($dateDebut)) {
            throw new InvalidArgumentException('La date de début doit être au format Y-m-d.');
        }
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function setDateFin(?string $dateFin): self
    {
        if ($dateFin !== null && !isValidDate($dateFin)) {
            throw new InvalidArgumentException('La date de fin doit être au format Y-m-d.');
        }
        if ($this->dateDebut && $dateFin && $dateFin < $this->dateDebut) {
            throw new InvalidArgumentException('La date de fin doit être postérieure ou égale à la date de début.');
        }
        $this->dateFin = $dateFin;
        return $this;
    }

    public function setExceptionnelle(bool $exceptionnelle): self
    {
        $this->exceptionnelle = $exceptionnelle;
        return $this;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'id_lieu' => $this->getIdLieu(),
            'num_jour' => $this->getNumJour(),
            'heure_debut' => $this->getHeureDebut(),
            'heure_fin' => $this->getHeureFin(),
            'date_debut' => $this->getDateDebut(),
            'date_fin' => $this->getDateFin(),
            'exceptionnelle' => $this->getExceptionnelle(),
            'is_closed' => $this->getIsClosed(),
        ];
    }
}
