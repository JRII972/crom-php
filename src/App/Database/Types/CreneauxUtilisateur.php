<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Enumération pour le type de créneau.
 */
enum TypeCreneau: string
{
    case Disponibilite = 'DISPONIBILITE';
    case Indisponibilite = 'INDISPONIBILITE';
}

/**
 * Classe représentant un créneau d'utilisateur dans la base de données.
 */
class CreneauxUtilisateur extends DefaultDatabaseType
{
    private ?string $idUtilisateur;
    private ?Utilisateur $utilisateur = null;
    private TypeCreneau $typeCreneau;
    private string $dateHeureDebut;
    private string $dateHeureFin;
    private bool $estRecurrant;
    private ?string $regleRecurrence = null;

    /**
     * Constructeur de la classe CreneauxUtilisateur.
     *
     * @param int|null $id Identifiant du créneau (si fourni, charge depuis la base)
     * @param Utilisateur|string|null $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur (requis si $id est null)
     * @param TypeCreneau|null $typeCreneau Type de créneau (requis si $id est null)
     * @param string|null $dateHeureDebut Date et heure de début (format Y-m-d H:i:s, requis si $id est null)
     * @param string|null $dateHeureFin Date et heure de fin (format Y-m-d H:i:s, requis si $id est null)
     * @param bool|null $estRecurrant Indique si le créneau est récurrent (requis si $id est null)
     * @param string|null $regleRecurrence Règle de récurrence (optionnel)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si le créneau n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Utilisateur|string|null $utilisateurOuId = null,
        ?TypeCreneau $typeCreneau = null,
        ?string $dateHeureDebut = null,
        ?string $dateHeureFin = null,
        ?bool $estRecurrant = null,
        ?string $regleRecurrence = null
    ) {
        parent::__construct();
        $this->table = 'creneaux_utilisateur';

        if ($id !== null && $utilisateurOuId === null && $typeCreneau === null && $dateHeureDebut === null && 
            $dateHeureFin === null && $estRecurrant === null && $regleRecurrence === null) {
            // Mode : Charger le créneau depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $utilisateurOuId !== null && $typeCreneau !== null && 
                  $dateHeureDebut !== null && $dateHeureFin !== null && $estRecurrant !== null) {
            // Mode : Créer un nouveau créneau
            $this->setUtilisateur($utilisateurOuId);
            $this->setTypeCreneau($typeCreneau);
            $this->setDateHeureDebut($dateHeureDebut);
            $this->setDateHeureFin($dateHeureFin);
            $this->setEstRecurrant($estRecurrant);
            if ($regleRecurrence !== null) {
                $this->setRegleRecurrence($regleRecurrence);
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit utilisateurOuId, typeCreneau, dateHeureDebut, ' .
                'dateHeureFin, et estRecurrant (et éventuellement regleRecurrence).'
            );
        }
    }

    /**
     * Charge les données du créneau depuis la base de données.
     *
     * @param int $id Identifiant du créneau
     * @throws PDOException Si le créneau n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM creneaux_utilisateur WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Créneau non trouvé pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->idUtilisateur = $data['id_utilisateur'];
        $this->typeCreneau = TypeCreneau::from($data['type_creneau']);
        $this->dateHeureDebut = $data['date_heure_debut'];
        $this->dateHeureFin = $data['date_heure_fin'];
        $this->estRecurrant = (bool) $data['est_recurrant'];
        $this->regleRecurrence = $data['regle_recurrence'];
    }

    /**
     * Sauvegarde le créneau dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE creneaux_utilisateur SET
                    id_utilisateur = :id_utilisateur,
                    type_creneau = :type_creneau,
                    date_heure_debut = :date_heure_debut,
                    date_heure_fin = :date_heure_fin,
                    est_recurrant = :est_recurrant,
                    regle_recurrence = :regle_recurrence
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_utilisateur' => $this->idUtilisateur,
                'type_creneau' => $this->typeCreneau->value,
                'date_heure_debut' => $this->dateHeureDebut,
                'date_heure_fin' => $this->dateHeureFin,
                'est_recurrant' => (int) $this->estRecurrant,
                'regle_recurrence' => $this->regleRecurrence,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO creneaux_utilisateur (
                    id_utilisateur, type_creneau, date_heure_debut, date_heure_fin, est_recurrant, regle_recurrence
                )
                VALUES (
                    :id_utilisateur, :type_creneau, :date_heure_debut, :date_heure_fin, :est_recurrant, :regle_recurrence
                )
            ');
            $stmt->execute([
                'id_utilisateur' => $this->idUtilisateur,
                'type_creneau' => $this->typeCreneau->value,
                'date_heure_debut' => $this->dateHeureDebut,
                'date_heure_fin' => $this->dateHeureFin,
                'est_recurrant' => (int) $this->estRecurrant,
                'regle_recurrence' => $this->regleRecurrence,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime le créneau de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un créneau sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM creneaux_utilisateur WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }

    /**
     * Recherche des créneaux avec filtre optionnel par utilisateur ou type de créneau.
     *
     * @param PDO $pdo Instance PDO
     * @param string $idUtilisateur ID de l'utilisateur (optionnel)
     * @param string $typeCreneau Type de créneau (optionnel, DISPONIBILITE ou INDISPONIBILITE)
     * @return array Liste des créneaux
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $idUtilisateur = '', string $typeCreneau = ''): array
    {
        $sql = 'SELECT id, id_utilisateur, type_creneau, date_heure_debut, date_heure_fin, est_recurrant, regle_recurrence FROM creneaux_utilisateur WHERE 1=1';
        $params = [];

        if ($idUtilisateur !== '') {
            $sql .= ' AND id_utilisateur = :id_utilisateur';
            $params['id_utilisateur'] = $idUtilisateur;
        }
        if ($typeCreneau !== '' && in_array($typeCreneau, [TypeCreneau::Disponibilite->value, TypeCreneau::Indisponibilite->value], true)) {
            $sql .= ' AND type_creneau = :type_creneau';
            $params['type_creneau'] = $typeCreneau;
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

    public function getUtilisateur(): ?Utilisateur
    {
        if ($this->utilisateur === null && $this->idUtilisateur !== null) {
            try {
                $this->utilisateur = new Utilisateur($this->idUtilisateur);
            } catch (PDOException) {
                $this->idUtilisateur = null;
            }
        }
        return $this->utilisateur;
    }

    public function getTypeCreneau(): TypeCreneau
    {
        return $this->typeCreneau;
    }

    public function getDateHeureDebut(): string
    {
        return $this->dateHeureDebut;
    }

    public function getDateHeureFin(): string
    {
        return $this->dateHeureFin;
    }

    public function getEstRecurrant(): bool
    {
        return $this->estRecurrant;
    }

    public function getRegleRecurrence(): ?string
    {
        return $this->regleRecurrence;
    }

    // Setters

    public function setUtilisateur(Utilisateur|string $utilisateur): self
    {
        if ($utilisateur instanceof Utilisateur) {
            $this->utilisateur = $utilisateur;
            $this->idUtilisateur = $utilisateur->getId();
        } else {
            if (!isValidUuid($utilisateur)) {
                throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
            }
            $this->idUtilisateur = $utilisateur;
            $this->utilisateur = null; // Lazy loading
        }
        return $this;
    }

    public function setTypeCreneau(TypeCreneau $typeCreneau): self
    {
        $this->typeCreneau = $typeCreneau;
        return $this;
    }

    public function setDateHeureDebut(string $dateHeureDebut): self
    {
        if (!isValidDateTime($dateHeureDebut)) {
            throw new InvalidArgumentException('La date et heure de début doivent être au format Y-m-d H:i:s.');
        }
        $this->dateHeureDebut = $dateHeureDebut;
        return $this;
    }

    public function setDateHeureFin(string $dateHeureFin): self
    {
        if (!isValidDateTime($dateHeureFin)) {
            throw new InvalidArgumentException('La date et heure de fin doivent être au format Y-m-d H:i:s.');
        }
        if ($this->dateHeureDebut) {
            $debut = DateTime::createFromFormat('Y-m-d H:i:s', $this->dateHeureDebut);
            $fin = DateTime::createFromFormat('Y-m-d H:i:s', $dateHeureFin);
            if ($fin <= $debut) {
                throw new InvalidArgumentException('La date et heure de fin doivent être postérieures à la date et heure de début.');
            }
        }
        $this->dateHeureFin = $dateHeureFin;
        return $this;
    }

    public function setEstRecurrant(bool $estRecurrant): self
    {
        $this->estRecurrant = $estRecurrant;
        return $this;
    }

    public function setRegleRecurrence(?string $regleRecurrence): self
    {
        if ($regleRecurrence !== null && !$this->estRecurrant) {
            throw new InvalidArgumentException('Une règle de récurrence ne peut être définie que pour un créneau récurrent.');
        }
        $this->regleRecurrence = $regleRecurrence;
        return $this;
    }

    // Helper Methods

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'id_utilisateur' => $this->idUtilisateur,
            'type_creneau' => $this->getTypeCreneau()->value,
            'date_heure_debut' => $this->getDateHeureDebut(),
            'date_heure_fin' => $this->getDateHeureFin(),
            'est_recurrant' => $this->getEstRecurrant(),
            'regle_recurrence' => $this->getRegleRecurrence(),
        ];
    }
}