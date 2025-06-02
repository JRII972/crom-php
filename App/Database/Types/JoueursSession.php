<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant une inscription d'un utilisateur à une session dans la base de données.
 */
class JoueursSession extends DefaultDatabaseType
{
    private ?int $idSession;
    private ?Session $session = null;
    private ?string $idUtilisateur;
    private ?Utilisateur $utilisateur = null;
    private string $dateInscription;

    /**
     * Constructeur de la classe JoueursSession.
     *
     * @param int|null $idSession ID de la session (requis si chargement ou création)
     * @param string|null $idUtilisateur ID de l'utilisateur (requis si chargement ou création)
     * @param Session|int|null $sessionOuId Objet Session ou ID de la session (optionnel pour création)
     * @param Utilisateur|string|null $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur (optionnel pour création)
     * @param string|null $dateInscription Date d'inscription (format Y-m-d H:i:s, optionnel pour création)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si l'enregistrement n'existe pas dans la base
     */
    public function __construct(
        ?int $idSession = null,
        ?string $idUtilisateur = null,
        Session|int|null $sessionOuId = null,
        Utilisateur|string|null $utilisateurOuId = null,
        ?string $dateInscription = null
    ) {
        parent::__construct();
        $this->table = 'joueurs_session';

        if ($idSession !== null && $idUtilisateur !== null && $sessionOuId === null && $utilisateurOuId === null && $dateInscription === null) {
            // Mode : Charger l'inscription depuis la base
            $this->loadFromDatabase($idSession, $idUtilisateur);
        } elseif ($idSession === null && $idUtilisateur === null && ($sessionOuId !== null || $utilisateurOuId !== null)) {
            // Mode : Créer une nouvelle inscription
            if ($sessionOuId === null || $utilisateurOuId === null) {
                throw new InvalidArgumentException('Pour créer une inscription, sessionOuId et utilisateurOuId sont requis.');
            }
            $this->setSession($sessionOuId);
            $this->setUtilisateur($utilisateurOuId);
            $this->dateInscription = $dateInscription ?? date('Y-m-d H:i:s');
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit idSession et idUtilisateur pour charger, soit sessionOuId et utilisateurOuId pour créer.'
            );
        }
    }

    /**
     * Charge les données de l'inscription depuis la base de données.
     *
     * @param int $idSession ID de la session
     * @param string $idUtilisateur ID de l'utilisateur
     * @throws PDOException Si l'inscription n'existe pas
     * @throws InvalidArgumentException Si l'ID utilisateur est invalide
     */
    private function loadFromDatabase(int $idSession, string $idUtilisateur): void
    {
        if (!isValidUuid($idUtilisateur)) {
            throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
        }

        $stmt = $this->pdo->prepare('SELECT * FROM joueurs_session WHERE id_session = :id_session AND id_utilisateur = :id_utilisateur');
        $stmt->execute([
            'id_session' => $idSession,
            'id_utilisateur' => $idUtilisateur,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Inscription non trouvée pour id_session : ' . $idSession . ' et id_utilisateur : ' . $idUtilisateur);
        }

        $this->idSession = (int) $data['id_session'];
        $this->idUtilisateur = $data['id_utilisateur'];
        $this->dateInscription = $data['date_inscription'];
    }

    /**
     * Sauvegarde l'inscription dans la base de données (insertion uniquement, mise à jour non supportée).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        // Vérifier si l'inscription existe déjà
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM joueurs_session WHERE id_session = :id_session AND id_utilisateur = :id_utilisateur');
        $stmt->execute([
            'id_session' => $this->idSession,
            'id_utilisateur' => $this->idUtilisateur,
        ]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            throw new PDOException('L\'inscription existe déjà pour cette session et cet utilisateur.');
        }

        // Insertion
        $stmt = $this->pdo->prepare('
            INSERT INTO joueurs_session (id_session, id_utilisateur, date_inscription)
            VALUES (:id_session, :id_utilisateur, :date_inscription)
        ');
        $stmt->execute([
            'id_session' => $this->idSession,
            'id_utilisateur' => $this->idUtilisateur,
            'date_inscription' => $this->dateInscription,
        ]);
    }

    /**
     * Supprime l'inscription de la base de données.
     *
     * @throws InvalidArgumentException Si les IDs ne sont pas définis
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->idSession) || !isset($this->idUtilisateur)) {
            throw new InvalidArgumentException('Impossible de supprimer une inscription sans id_session et id_utilisateur.');
        }

        $stmt = $this->pdo->prepare('DELETE FROM joueurs_session WHERE id_session = :id_session AND id_utilisateur = :id_utilisateur');
        $stmt->execute([
            'id_session' => $this->idSession,
            'id_utilisateur' => $this->idUtilisateur,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucune inscription supprimée : inscription non trouvée.');
        }

        return true;
    }

    /**
     * Recherche des inscriptions avec filtre optionnel par session ou utilisateur.
     *
     * @param PDO $pdo Instance PDO
     * @param int $idSession ID de la session (optionnel)
     * @param string $idUtilisateur ID de l'utilisateur (optionnel)
     * @return array Liste des inscriptions
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, int $idSession = 0, string $idUtilisateur = ''): array
    {
        $sql = 'SELECT id_session, id_utilisateur, date_inscription FROM joueurs_session WHERE 1=1';
        $params = [];

        if ($idSession > 0) {
            $sql .= ' AND id_session = :id_session';
            $params['id_session'] = $idSession;
        }
        if ($idUtilisateur !== '') {
            $sql .= ' AND id_utilisateur = :id_utilisateur';
            $params['id_utilisateur'] = $idUtilisateur;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters

    public function getIdSession(): int
    {
        return $this->idSession;
    }

    public function getSession(): ?Session
    {
        if ($this->session === null && $this->idSession !== null) {
            try {
                $this->session = new Session($this->idSession);
            } catch (PDOException) {
                $this->idSession = null;
            }
        }
        return $this->session;
    }

    public function getIdUtilisateur(): string
    {
        return $this->idUtilisateur;
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

    public function getDateInscription(): string
    {
        return $this->dateInscription;
    }

    // Setters

    public function setSession(Session|int $session): self
    {
        if ($session instanceof Session) {
            $this->session = $session;
            $this->idSession = $session->getId();
        } else {
            $this->idSession = $session;
            $this->session = null; // Lazy loading
        }
        return $this;
    }

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

    public function setDateInscription(string $dateInscription): self
    {
        if (!isValidDateTime($dateInscription)) {
            throw new InvalidArgumentException('La date d\'inscription doit être au format Y-m-d H:i:s.');
        }
        $this->dateInscription = $dateInscription;
        return $this;
    }

    // Helper Methods



    public function jsonSerialize(): array
    {
        return [
            'id_session' => $this->getIdSession(),
            'id_utilisateur' => $this->getIdUtilisateur(),
            'utilisateur' => $this->getUtilisateur()->jsonSerialize(),
            'date_inscription' => $this->getDateInscription(),
        ];
    }
}