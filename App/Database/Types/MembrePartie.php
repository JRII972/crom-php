<?php

declare(strict_types=1);

namespace App\Database\Types;

use PDO;
use PDOException;
use InvalidArgumentException;

/**
 * Classe représentant un membre d'une partie dans la base de données.
 */
class MembrePartie extends DefaultDatabaseType
{
    private int $idPartie;
    private string $idUtilisateur;
    private ?Partie $partie = null;
    private ?Utilisateur $utilisateur = null;

    /**
     * Constructeur de la classe MembrePartie.
     *
     * @param Partie|int $partieOuId Objet Partie ou ID de la partie
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @throws InvalidArgumentException Si les paramètres sont invalides
     */
    public function __construct(
        Partie|int $partieOuId,
        Utilisateur|string $utilisateurOuId
    ) {
        parent::__construct();
        $this->table = 'membres_partie';

        $this->setPartie($partieOuId);
        $this->setUtilisateur($utilisateurOuId);
    }

    /**
     * Sauvegarde le membre de la partie dans la base de données (insertion).
     *
     * @throws PDOException En cas d'erreur SQL
     * @throws InvalidArgumentException Si la partie est pleine
     */
    public function save(): void
    {
        // Vérifier si la partie a de la place
        $partie = $this->getPartie();
        if (!$partie->restePlace()) {
            throw new InvalidArgumentException('La partie est pleine.');
        }

        // Vérifier si l'utilisateur est déjà membre
        if ($partie->estMembre($this->idUtilisateur)) {
            throw new InvalidArgumentException('L\'utilisateur est déjà membre de cette partie.');
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO membres_partie (id_partie, id_utilisateur)
            VALUES (:id_partie, :id_utilisateur)
        ');
        $stmt->execute([
            'id_partie' => $this->idPartie,
            'id_utilisateur' => $this->idUtilisateur,
        ]);
    }

    /**
     * Supprime le membre de la partie de la base de données.
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM membres_partie
            WHERE id_partie = :id_partie AND id_utilisateur = :id_utilisateur
        ');
        $stmt->execute([
            'id_partie' => $this->idPartie,
            'id_utilisateur' => $this->idUtilisateur,
        ]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucun membre supprimé : membre non trouvé.');
        }

        return true;
    }

    /**
     * Recherche des membres de parties avec filtre optionnel par partie ou utilisateur.
     *
     * @param PDO $pdo Instance PDO
     * @param int $idPartie ID de la partie (optionnel)
     * @param string $idUtilisateur ID de l'utilisateur (optionnel)
     * @return array Liste des membres
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, int $idPartie = 0, string $idUtilisateur = ''): array
    {
        $sql = 'SELECT id_partie, id_utilisateur FROM membres_partie WHERE 1=1';
        $params = [];

        if ($idPartie > 0) {
            $sql .= ' AND id_partie = :id_partie';
            $params['id_partie'] = $idPartie;
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

    /**
     * Récupère l'ID de la partie.
     *
     * @return int
     */
    public function getIdPartie(): int
    {
        return $this->idPartie;
    }

    /**
     * Récupère l'ID de l'utilisateur.
     *
     * @return string
     */
    public function getIdUtilisateur(): string
    {
        return $this->idUtilisateur;
    }

    /**
     * Récupère l'objet Partie.
     *
     * @return Partie
     * @throws PDOException Si la partie n'existe pas
     */
    public function getPartie(): Partie
    {
        if ($this->partie === null) {
            $this->partie = new Partie($this->idPartie);
        }
        return $this->partie;
    }

    /**
     * Récupère l'objet Utilisateur.
     *
     * @return Utilisateur
     * @throws PDOException Si l'utilisateur n'existe pas
     */
    public function getUtilisateur(): Utilisateur
    {
        if ($this->utilisateur === null) {
            $this->utilisateur = new Utilisateur($this->idUtilisateur);
        }
        return $this->utilisateur;
    }

    // Setters

    /**
     * Définit la partie.
     *
     * @param Partie|int $partieOuId Objet Partie ou ID de la partie
     * @return self
     * @throws InvalidArgumentException Si l'ID est invalide
     */
    public function setPartie(Partie|int $partieOuId): self
    {
        if ($partieOuId instanceof Partie) {
            $this->partie = $partieOuId;
            $this->idPartie = $partieOuId->getId();
        } else {
            if (!is_int($partieOuId) || $partieOuId <= 0) {
                throw new InvalidArgumentException('L\'ID de la partie doit être un entier positif.');
            }
            $this->idPartie = $partieOuId;
            $this->partie = null; // Lazy loading
        }
        return $this;
    }

    /**
     * Définit l'utilisateur.
     *
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @return self
     * @throws InvalidArgumentException Si l'ID n'est pas un UUID valide
     */
    public function setUtilisateur(Utilisateur|string $utilisateurOuId): self
    {
        if ($utilisateurOuId instanceof Utilisateur) {
            $this->utilisateur = $utilisateurOuId;
            $this->idUtilisateur = $utilisateurOuId->getId();
        } else {
            if (!isValidUuid($utilisateurOuId)) {
                throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
            }
            $this->idUtilisateur = $utilisateurOuId;
            $this->utilisateur = null; // Lazy loading
        }
        return $this;
    }

    // Helper Methods

    /**
     * Sérialisation JSON de l'objet.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id_partie' => $this->getIdPartie(),
            'id_utilisateur' => $this->getIdUtilisateur(),
        ];
    }
}