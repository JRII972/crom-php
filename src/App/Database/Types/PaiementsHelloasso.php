<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant un paiement Helloasso dans la base de données.
 */
class PaiementsHelloasso extends DefaultDatabaseType
{
    private ?string $idNotification = null;
    private ?NotificationsHelloasso $notification = null;
    private ?string $idUtilisateur = null;
    private ?Utilisateur $utilisateur = null;
    private ?string $typePaiement = null;
    private ?string $nom = null;
    private float $montant;
    private string $devise;
    private ?string $dateEcheance = null;
    private ?string $statut = null;
    private ?string $metadonnees = null;
    private string $dateCreation;

    /**
     * Constructeur de la classe PaiementsHelloasso.
     *
     * @param string|null $id Identifiant du paiement (si fourni, charge depuis la base)
     * @param NotificationsHelloasso|string|null $notificationOuId Objet NotificationsHelloasso ou ID de la notification (optionnel)
     * @param Utilisateur|string|null $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur (optionnel)
     * @param string|null $typePaiement Type de paiement (optionnel)
     * @param string|null $nom Nom associé au paiement (optionnel)
     * @param float|null $montant Montant du paiement (requis si $id est null)
     * @param string|null $devise Devise du paiement (requis si $id est null)
     * @param string|null $dateEcheance Date d'échéance (format Y-m-d, optionnel)
     * @param string|null $statut Statut du paiement (optionnel)
     * @param string|null $metadonnees Métadonnées JSON (optionnel)
     * @param string|null $dateCreation Date de création (format Y-m-d H:i:s, optionnel, défaut CURRENT_TIMESTAMP)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si le paiement n'existe pas dans la base
     */
    public function __construct(
        ?string $id = null,
        NotificationsHelloasso|string|null $notificationOuId = null,
        Utilisateur|string|null $utilisateurOuId = null,
        ?string $typePaiement = null,
        ?string $nom = null,
        ?float $montant = null,
        ?string $devise = null,
        ?string $dateEcheance = null,
        ?string $statut = null,
        ?string $metadonnees = null,
        ?string $dateCreation = null
    ) {
        parent::__construct();
        $this->table = 'paiements_helloasso';

        if ($id !== null && $notificationOuId === null && $utilisateurOuId === null && $typePaiement === null && 
            $nom === null && $montant === null && $devise === null && $dateEcheance === null && 
            $statut === null && $metadonnees === null && $dateCreation === null) {
            // Mode : Charger le paiement depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $montant !== null && $devise !== null) {
            // Mode : Créer un nouveau paiement
            $this->setId(generateUUID());
            if ($notificationOuId !== null) {
                $this->setNotification($notificationOuId);
            }
            if ($utilisateurOuId !== null) {
                $this->setUtilisateur($utilisateurOuId);
            }
            if ($typePaiement !== null) {
                $this->setTypePaiement($typePaiement);
            }
            if ($nom !== null) {
                $this->setNom($nom);
            }
            $this->setMontant($montant);
            $this->setDevise($devise);
            if ($dateEcheance !== null) {
                $this->setDateEcheance($dateEcheance);
            }
            if ($statut !== null) {
                $this->setStatut($statut);
            }
            if ($metadonnees !== null) {
                $this->setMetadonnees($metadonnees);
            }
            $this->setDateCreation($dateCreation ?? date('Y-m-d H:i:s'));
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit montant et devise ' .
                '(et éventuellement notificationOuId, utilisateurOuId, typePaiement, nom, dateEcheance, statut, metadonnees, dateCreation).'
            );
        }
    }

    /**
     * Charge les données du paiement depuis la base de données.
     *
     * @param string $id Identifiant du paiement
     * @throws PDOException Si le paiement n'existe pas
     */
    private function loadFromDatabase(string $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM paiements_helloasso WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Paiement non trouvé pour l\'ID : ' . $id);
        }

        $this->id = $data['id'];
        $this->idNotification = $data['id_notification'];
        $this->idUtilisateur = $data['id_utilisateur'];
        $this->typePaiement = $data['type_paiement'];
        $this->nom = $data['nom'];
        $this->montant = (float) $data['montant'];
        $this->devise = $data['devise'];
        $this->dateEcheance = $data['date_echeance'];
        $this->statut = $data['statut'];
        $this->metadonnees = $data['metadonnees'];
        $this->dateCreation = $data['date_creation'];
    }

    /**
     * Sauvegarde le paiement dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        // Vérifier si le paiement existe déjà
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM paiements_helloasso WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE paiements_helloasso SET
                    id_notification = :id_notification,
                    id_utilisateur = :id_utilisateur,
                    type_paiement = :type_paiement,
                    nom = :nom,
                    montant = :montant,
                    devise = :devise,
                    date_echeance = :date_echeance,
                    statut = :statut,
                    metadonnees = :metadonnees,
                    date_creation = :date_creation
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_notification' => $this->idNotification,
                'id_utilisateur' => $this->idUtilisateur,
                'type_paiement' => $this->typePaiement,
                'nom' => $this->nom,
                'montant' => $this->montant,
                'devise' => $this->devise,
                'date_echeance' => $this->dateEcheance,
                'statut' => $this->statut,
                'metadonnees' => $this->metadonnees,
                'date_creation' => $this->dateCreation,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO paiements_helloasso (
                    id, id_notification, id_utilisateur, type_paiement, nom, montant, 
                    devise, date_echeance, statut, metadonnees, date_creation
                )
                VALUES (
                    :id, :id_notification, :id_utilisateur, :type_paiement, :nom, :montant, 
                    :devise, :date_echeance, :statut, :metadonnees, :date_creation
                )
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_notification' => $this->idNotification,
                'id_utilisateur' => $this->idUtilisateur,
                'type_paiement' => $this->typePaiement,
                'nom' => $this->nom,
                'montant' => $this->montant,
                'devise' => $this->devise,
                'date_echeance' => $this->dateEcheance,
                'statut' => $this->statut,
                'metadonnees' => $this->metadonnees,
                'date_creation' => $this->dateCreation,
            ]);
        }
    }

    /**
     * Supprime le paiement de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un paiement sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM paiements_helloasso WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucun paiement supprimé : paiement non trouvé.');
        }

        return true;
    }

    /**
     * Recherche des paiements avec filtre optionnel par utilisateur ou statut.
     *
     * @param PDO $pdo Instance PDO
     * @param string $idUtilisateur ID de l'utilisateur (optionnel)
     * @param string $statut Statut du paiement (optionnel)
     * @return array Liste des paiements
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $idUtilisateur = '', string $statut = ''): array
    {
        $sql = 'SELECT id, id_notification, id_utilisateur, type_paiement, nom, montant, devise, date_echeance, statut, metadonnees, date_creation FROM paiements_helloasso WHERE 1=1';
        $params = [];

        if ($idUtilisateur !== '') {
            $sql .= ' AND id_utilisateur = :id_utilisateur';
            $params['id_utilisateur'] = $idUtilisateur;
        }
        if ($statut !== '') {
            $sql .= ' AND statut = :statut';
            $params['statut'] = $statut;
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

    public function getNotification(): ?NotificationsHelloasso
    {
        if ($this->notification === null && $this->idNotification !== null) {
            try {
                $this->notification = new NotificationsHelloasso($this->idNotification);
            } catch (PDOException) {
                $this->idNotification = null;
            }
        }
        return $this->notification;
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

    public function getTypePaiement(): ?string
    {
        return $this->typePaiement;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getDevise(): string
    {
        return $this->devise;
    }

    public function getDateEcheance(): ?string
    {
        return $this->dateEcheance;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function getMetadonnees(): ?string
    {
        return $this->metadonnees;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
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

    public function setNotification(NotificationsHelloasso|string|null $notification): self
    {
        if ($notification instanceof NotificationsHelloasso) {
            $this->notification = $notification;
            $this->idNotification = $notification->getId();
        } elseif (is_string($notification)) {
            if (empty(trim($notification))) {
                throw new InvalidArgumentException('L\'ID de la notification ne peut pas être vide.');
            }
            $this->idNotification = $notification;
            $this->notification = null; // Lazy loading
        } else {
            $this->idNotification = null;
            $this->notification = null;
        }
        return $this;
    }

    public function setUtilisateur(Utilisateur|string|null $utilisateur): self
    {
        if ($utilisateur instanceof Utilisateur) {
            $this->utilisateur = $utilisateur;
            $this->idUtilisateur = $utilisateur->getId();
        } elseif (is_string($utilisateur)) {
            if (!isValidUuid($utilisateur)) {
                throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
            }
            $this->idUtilisateur = $utilisateur;
            $this->utilisateur = null; // Lazy loading
        } else {
            $this->idUtilisateur = null;
            $this->utilisateur = null;
        }
        return $this;
    }

    public function setTypePaiement(?string $typePaiement): self
    {
        if ($typePaiement !== null && strlen($typePaiement) > 100) {
            throw new InvalidArgumentException('Le type de paiement ne peut pas dépasser 100 caractères.');
        }
        $this->typePaiement = $typePaiement;
        return $this;
    }

    public function setNom(?string $nom): self
    {
        if ($nom !== null && strlen($nom) > 255) {
            throw new InvalidArgumentException('Le nom ne peut pas dépasser 255 caractères.');
        }
        $this->nom = $nom;
        return $this;
    }

    public function setMontant(float $montant): self
    {
        if ($montant < 0) {
            throw new InvalidArgumentException('Le montant ne peut pas être négatif.');
        }
        $this->montant = $montant;
        return $this;
    }

    public function setDevise(string $devise): self
    {
        if (empty(trim($devise))) {
            throw new InvalidArgumentException('La devise ne peut pas être vide.');
        }
        if (strlen($devise) > 10) {
            throw new InvalidArgumentException('La devise ne peut pas dépasser 10 caractères.');
        }
        $this->devise = $devise;
        return $this;
    }

    public function setDateEcheance(?string $dateEcheance): self
    {
        if ($dateEcheance !== null && !isValidDate($dateEcheance)) {
            throw new InvalidArgumentException('La date d\'échéance doit être au format Y-m-d.');
        }
        $this->dateEcheance = $dateEcheance;
        return $this;
    }

    public function setStatut(?string $statut): self
    {
        if ($statut !== null && strlen($statut) > 50) {
            throw new InvalidArgumentException('Le statut ne peut pas dépasser 50 caractères.');
        }
        $this->statut = $statut;
        return $this;
    }

    public function setMetadonnees(?string $metadonnees): self
    {
        if ($metadonnees !== null && !isValidJson($metadonnees)) {
            throw new InvalidArgumentException('Les métadonnées doivent être un JSON valide.');
        }
        $this->metadonnees = $metadonnees;
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
            'id_notification' => $this->idNotification,
            'id_utilisateur' => $this->idUtilisateur,
            'notification' => $this->getNotification(),
            'utilisateur' => $this->getUtilisateur(),
            'type_paiement' => $this->getTypePaiement(),
            'nom' => $this->getNom(),
            'montant' => $this->getMontant(),
            'devise' => $this->getDevise(),
            'date_echeance' => $this->getDateEcheance(),
            'statut' => $this->getStatut(),
            'metadonnees' => $this->getMetadonnees(),
            'date_creation' => $this->getDateCreation(),
        ];
    }
}