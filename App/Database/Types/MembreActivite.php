<?php

declare(strict_types=1);

namespace App\Database\Types;

use PDO;
use PDOException;
use InvalidArgumentException;

/**
 * Classe représentant un membre d'une activite dans la base de données.
 */
class MembreActivite extends DefaultDatabaseType
{
    private int $idActivite;
    private string $idUtilisateur;
    private ?Activite $activite = null;
    private ?Utilisateur $utilisateur = null;

    /**
     * Constructeur de la classe MembreActivite.
     *
     * @param Activite|int $activiteOuId Objet Activite ou ID de la activite
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @throws InvalidArgumentException Si les paramètres sont invalides
     */
    public function __construct(
        Activite|int $activiteOuId,
        Utilisateur|string $utilisateurOuId
    ) {
        parent::__construct();
        $this->table = 'membres_activite';

        $this->setActivite($activiteOuId);
        $this->setUtilisateur($utilisateurOuId);
    }

    /**
     * Sauvegarde le membre de la activite dans la base de données (insertion).
     *
     * @throws PDOException En cas d'erreur SQL
     * @throws InvalidArgumentException Si la activite est pleine
     */
    public function save(): void
    {
        // Vérifier si la activite a de la place
        $activite = $this->getActivite();
        if (!$activite->restePlace()) {
            throw new InvalidArgumentException('La activite est pleine.');
        }

        // Vérifier si l'utilisateur est déjà membre
        if ($activite->estMembre($this->idUtilisateur)) {
            throw new InvalidArgumentException('L\'utilisateur est déjà membre de cette activite.');
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO membres_activite (id_activite, id_utilisateur)
            VALUES (:id_activite, :id_utilisateur)
        ');
        $stmt->execute([
            'id_activite' => $this->idActivite,
            'id_utilisateur' => $this->idUtilisateur,
        ]);
    }

    /**
     * Supprime le membre de la activite de la base de données.
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM membres_activite
            WHERE id_activite = :id_activite AND id_utilisateur = :id_utilisateur
        ');
        $stmt->execute([
            'id_activite' => $this->idActivite,
            'id_utilisateur' => $this->idUtilisateur,
        ]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucun membre supprimé : membre non trouvé.');
        }

        return true;
    }

    /**
     * Recherche des membres de activites avec filtre optionnel par activite ou utilisateur.
     *
     * @param PDO $pdo Instance PDO
     * @param int $idActivite ID de la activite (optionnel)
     * @param string $idUtilisateur ID de l'utilisateur (optionnel)
     * @return array Liste des membres
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, int $idActivite = 0, string $idUtilisateur = ''): array
    {
        $sql = 'SELECT id_activite, id_utilisateur FROM membres_activite WHERE 1=1';
        $params = [];

        if ($idActivite > 0) {
            $sql .= ' AND id_activite = :id_activite';
            $params['id_activite'] = $idActivite;
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
     * Récupère l'ID de la activite.
     *
     * @return int
     */
    public function getIdActivite(): int
    {
        return $this->idActivite;
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
     * Récupère l'objet Activite.
     *
     * @return Activite
     * @throws PDOException Si la activite n'existe pas
     */
    public function getActivite(): Activite
    {
        if ($this->activite === null) {
            $this->activite = new Activite($this->idActivite);
        }
        return $this->activite;
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
     * Définit la activite.
     *
     * @param Activite|int $activiteOuId Objet Activite ou ID de la activite
     * @return self
     * @throws InvalidArgumentException Si l'ID est invalide
     */
    public function setActivite(Activite|int $activiteOuId): self
    {
        if ($activiteOuId instanceof Activite) {
            $this->activite = $activiteOuId;
            $this->idActivite = $activiteOuId->getId();
        } else {
            if (!is_int($activiteOuId) || $activiteOuId <= 0) {
                throw new InvalidArgumentException('L\'ID de la activite doit être un entier positif.');
            }
            $this->idActivite = $activiteOuId;
            $this->activite = null; // Lazy loading
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
            'id_activite' => $this->getIdActivite(),
            'id_utilisateur' => $this->getIdUtilisateur(),
        ];
    }
}