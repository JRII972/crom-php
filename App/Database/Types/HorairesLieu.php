<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

require_once __DIR__ . '/../../Utils/helpers.php';
require_once __DIR__ . '/../../Utils/validate_rrule_json.php';

/**
 * Enumération pour le type de récurrence.
 */
enum TypeRecurrence: string
{
    case Aucune = 'AUCUNE';
    case Quotidienne = 'QUOTIDIENNE';
    case Hebdomadaire = 'HEBDOMADAIRE';
    case Mensuelle = 'MENSUELLE';
    case Annuelle = 'ANNUELLE';
}

/**
 * Classe représentant un horaire de lieu dans la base de données.
 */
class HorairesLieu extends DefaultDatabaseType
{
    private ?int $idLieu;
    private ?Lieu $lieu = null;
    private string $heureDebut;
    private string $heureFin;
    private TypeRecurrence $typeRecurrence;
    private ?string $regleRecurrence = null;
    private ?string $exceptions = null;
    private ?int $idEvenement = null;
    private ?Evenement $evenement = null;

    /**
     * Constructeur de la classe HorairesLieu.
     *
     * @param int|null $id Identifiant de l'horaire (si fourni, charge depuis la base)
     * @param Lieu|int|null $lieuOuId Objet Lieu ou ID du lieu (requis si $id est null)
     * @param string|null $heureDebut Heure de début (format H:i:s, requis si $id est null)
     * @param string|null $heureFin Heure de fin (format H:i:s, requis si $id est null)
     * @param TypeRecurrence|null $typeRecurrence Type de récurrence (requis si $id est null)
     * @param string|array|null $regleRecurrence Règle de récurrence (JSON, optionnel)
     * @param string|array|null $exceptions Exceptions (JSON, optionnel)
     * @param Evenement|int|null $evenementOuId Objet Evenement ou ID de l'événement (optionnel)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si l'horaire n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Lieu|int|null $lieuOuId = null,
        ?string $heureDebut = null,
        ?string $heureFin = null,
        ?TypeRecurrence $typeRecurrence = null,
        array|string|null $regleRecurrence = null,
        string|array|null $exceptions = null,
        Evenement|int|null $evenementOuId = null
    ) {
        parent::__construct();
        $this->table = 'horaires_lieu';

        if ($id !== null && $lieuOuId === null && $heureDebut === null && $heureFin === null && 
            $typeRecurrence === null && $regleRecurrence === null && $exceptions === null && 
            $evenementOuId === null) {
            // Mode : Charger l'horaire depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $lieuOuId !== null && $heureDebut !== null && $heureFin !== null && 
                  $typeRecurrence !== null) {
            // Mode : Créer un nouvel horaire
            $this->setLieu($lieuOuId);
            $this->setHeureDebut($heureDebut);
            $this->setHeureFin($heureFin);
            $this->setTypeRecurrence($typeRecurrence);
            if ($regleRecurrence !== null) {
                $this->setRegleRecurrence($regleRecurrence);
            }
            if ($exceptions !== null) {
                $this->setExceptions($exceptions);
            }
            if ($evenementOuId !== null) {
                $this->setEvenement($evenementOuId);
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit lieuOuId, heureDebut, heureFin, et typeRecurrence ' .
                '(et éventuellement regleRecurrence, exceptions, evenementOuId).'
            );
        }
    }

    /**
     * Charge les données de l'horaire depuis la base de données.
     *
     * @param int $id Identifiant de l'horaire
     * @throws PDOException Si l'horaire n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM horaires_lieu WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Horaire non trouvé pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->idLieu = (int) $data['id_lieu'];
        $this->heureDebut = $data['heure_debut'];
        $this->heureFin = $data['heure_fin'];
        $this->typeRecurrence = TypeRecurrence::from($data['type_recurrence']);
        $this->regleRecurrence = $data['regle_recurrence'];
        $this->exceptions = $data['exceptions'];
        $this->idEvenement = $data['id_evenement'] !== null ? (int) $data['id_evenement'] : null;
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
                    heure_debut = :heure_debut,
                    heure_fin = :heure_fin,
                    type_recurrence = :type_recurrence,
                    regle_recurrence = :regle_recurrence,
                    exceptions = :exceptions,
                    id_evenement = :id_evenement
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_lieu' => $this->idLieu,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'type_recurrence' => $this->typeRecurrence->value,
                'regle_recurrence' => $this->regleRecurrence,
                'exceptions' => $this->exceptions,
                'id_evenement' => $this->idEvenement,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO horaires_lieu (
                    id_lieu, heure_debut, heure_fin, type_recurrence, regle_recurrence, exceptions, id_evenement
                )
                VALUES (
                    :id_lieu, :heure_debut, :heure_fin, :type_recurrence, :regle_recurrence, :exceptions, :id_evenement
                )
            ');
            $stmt->execute([
                'id_lieu' => $this->idLieu,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'type_recurrence' => $this->typeRecurrence->value,
                'regle_recurrence' => $this->regleRecurrence,
                'exceptions' => $this->exceptions,
                'id_evenement' => $this->idEvenement,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime l'horaire de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un horaire sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM horaires_lieu WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * Recherche des horaires avec filtre optionnel par lieu ou type de récurrence.
     *
     * @param PDO $pdo Instance PDO
     * @param int $idLieu ID du lieu (optionnel)
     * @param string $typeRecurrence Type de récurrence (optionnel, AUCUNE, QUOTIDIENNE, HEBDOMADAIRE, MENSUELLE, ANNUELLE)
     * @return array Liste des horaires
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, int $idLieu = 0, string $typeRecurrence = ''): array
    {
        $sql = 'SELECT id, id_lieu, heure_debut, heure_fin, type_recurrence, regle_recurrence, exceptions, id_evenement FROM horaires_lieu WHERE 1=1';
        $params = [];

        if ($idLieu > 0) {
            $sql .= ' AND id_lieu = :id_lieu';
            $params['id_lieu'] = $idLieu;
        }
        if ($typeRecurrence !== '' && in_array($typeRecurrence, [
            TypeRecurrence::Aucune->value,
            TypeRecurrence::Quotidienne->value,
            TypeRecurrence::Hebdomadaire->value,
            TypeRecurrence::Mensuelle->value,
            TypeRecurrence::Annuelle->value
        ], true)) {
            $sql .= ' AND type_recurrence = :type_recurrence';
            $params['type_recurrence'] = $typeRecurrence;
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

    public function getHeureDebut(): string
    {
        return $this->heureDebut;
    }

    public function getHeureFin(): string
    {
        return $this->heureFin;
    }

    public function getTypeRecurrence(): TypeRecurrence
    {
        return $this->typeRecurrence;
    }

    public function getRegleRecurrence(): ?\stdClass 
    {
        return json_decode($this->regleRecurrence);
    }

    public function getExceptions(): ?array 
    {
        return json_decode($this->exceptions);
    }

    public function getEvenement(): ?Evenement
    {
        if ($this->evenement === null && $this->idEvenement !== null) {
            try {
                $this->evenement = new Evenement($this->idEvenement);
            } catch (PDOException) {
                $this->idEvenement = null;
            }
        }
        return $this->evenement;
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
        if ($this->heureDebut) {
            $debut = DateTime::createFromFormat('H:i:s', $this->heureDebut);
            $fin = DateTime::createFromFormat('H:i:s', $heureFin);
            if ($fin <= $debut) {
                throw new InvalidArgumentException('L\'heure de fin doit être postérieure à l\'heure de début.');
            }
        }
        $this->heureFin = $heureFin;
        return $this;
    }

    public function setTypeRecurrence(TypeRecurrence $typeRecurrence): self
    {
        $this->typeRecurrence = $typeRecurrence;
        return $this;
    }

    public function setRegleRecurrence(array|string|null $regleRecurrence): self
    {
        
        if ( is_array($regleRecurrence)){
            if (!validateRRuleJson($regleRecurrence)){
                throw new InvalidArgumentException('Les regle de recurrence doivent être au bon format.');
            }
            $this->regleRecurrence = json_encode($regleRecurrence);
        } else {
            if ($regleRecurrence !== null && !isValidJson($regleRecurrence)) {
                throw new InvalidArgumentException('La règle de récurrence doit être un JSON valide.');
            }
            if (!validateRRuleJson(json_decode($regleRecurrence))){
                throw new InvalidArgumentException('Les regle de recurrence doivent être au bon format.');
            }
            $this->regleRecurrence = $regleRecurrence;
        }
        return $this;
    }

    public function setExceptions(array|string|null $exceptions): self
    {
        

        if (is_array($exceptions)){            
            $this->exceptions = json_encode($exceptions);
        } else {
            if ($exceptions !== null && !isValidJson($exceptions)) {
                throw new InvalidArgumentException('Les exceptions doivent être un JSON valide.');
            }
            $this->exceptions = $exceptions;
        }

        return $this;
    }

    public function setEvenement(Evenement|int|null $evenement): self
    {
        if ($evenement instanceof Evenement) {
            $this->evenement = $evenement;
            $this->idEvenement = $evenement->getId();
        } elseif (is_int($evenement)) {
            $this->idEvenement = $evenement;
            $this->evenement = null; // Lazy loading
        } else {
            $this->idEvenement = null;
            $this->evenement = null;
        }
        return $this;
    }

    // Helper Methods

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'id_lieu' => $this->idLieu,
            'heure_debut' => $this->getHeureDebut(),
            'heure_fin' => $this->getHeureFin(),
            'type_recurrence' => $this->getTypeRecurrence()->value,
            'regle_recurrence' => $this->getRegleRecurrence(),
            'exceptions' => $this->getExceptions(),
            'id_evenement' => $this->idEvenement,
        ];
    }
}