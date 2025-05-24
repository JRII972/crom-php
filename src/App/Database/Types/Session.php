<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Classe représentant une session dans la base de données.
 */
class Session extends DefaultDatabaseType
{
    private ?int $idPartie;
    private ?Partie $partie = null;
    private ?int $idLieu;
    private ?Lieu $lieu = null;
    private string $dateSession;
    private string $heureDebut;
    private string $heureFin;
    private ?string $idMaitreJeu;
    private ?Utilisateur $maitreJeu = null;
    private ?int $maxJoueurs = null;

    // Cache configuration
    private static $cacheEnabled = true; // Activer/désactiver le cache
    private static $cacheTTL = 300; // 5 minutes en secondes
    private static $cachePrefix = 'session_search_';

    /**
     * Constructeur de la classe Session.
     *
     * @param int|null $id Identifiant de la session (si fourni, charge depuis la base)
     * @param Partie|int|null $partieOuId Objet Partie ou ID de la partie (requis si $id est null)
     * @param Lieu|int|null $lieuOuId Objet Lieu ou ID du lieu (requis si $id est null)
     * @param string|null $dateSession Date de la session (format Y-m-d, requis si $id est null)
     * @param string|null $heureDebut Heure de début (format H:i:s, requis si $id est null)
     * @param string|null $heureFin Heure de fin (format H:i:s, requis si $id est null)
     * @param Utilisateur|string|null $maitreJeuOuId Objet Utilisateur ou ID du maître du jeu (requis si $id est null)
     * @param int|null $maxJoueurs Nombre maximum de joueurs pour la session (optionnel)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si la session n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        Partie|int|null $partieOuId = null,
        Lieu|int|null $lieuOuId = null,
        ?string $dateSession = null,
        ?string $heureDebut = null,
        ?string $heureFin = null,
        Utilisateur|string|null $maitreJeuOuId = null,
        ?int $maxJoueurs = null
    ) {
        parent::__construct();
        $this->table = 'sessions';

        if ($id !== null && $partieOuId === null && $lieuOuId === null && $dateSession === null && 
            $heureDebut === null && $heureFin === null && $maitreJeuOuId === null && $maxJoueurs === null) {
            // Mode : Charger la session depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $partieOuId !== null && $lieuOuId !== null && $dateSession !== null && 
                  $heureDebut !== null && $heureFin !== null && $maitreJeuOuId !== null) {
            // Mode : Créer une nouvelle session
            $this->setPartie($partieOuId);
            $this->setLieu($lieuOuId);
            $this->setDateSession($dateSession);
            $this->setHeureDebut($heureDebut);
            $this->setHeureFin($heureFin);
            $this->setMaitreJeu($maitreJeuOuId);
            // Définir maxJoueurs par défaut
            if ($maxJoueurs === null && $this->partie !== null) {
                if ($this->partie->getMaxJoueursSession() !== null) {
                    $this->setMaxJoueurs($this->partie->getMaxJoueursSession());
                } else {
                    $this->setMaxJoueurs($this->partie->getNombreMaxJoueurs());
                }
            } else {
                $this->setMaxJoueurs($maxJoueurs);
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit partieOuId, lieuOuId, dateSession, heureDebut, heureFin, et maitreJeuOuId.'
            );
        }
    }

    /**
     * Charge les données de la session depuis la base de données.
     *
     * @param int $id Identifiant de la session
     * @throws PDOException Si la session n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM sessions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Session non trouvée pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->idPartie = (int) $data['id_partie'];
        $this->idLieu = (int) $data['id_lieu'];
        $this->dateSession = $data['date_session'];
        $this->heureDebut = $data['heure_debut'];
        $this->heureFin = $data['heure_fin'];
        $this->idMaitreJeu = $data['id_maitre_jeu'];
        $this->maxJoueurs = isset($data['max_joueurs']) ? (int) $data['max_joueurs'] : null;
    }

    /**
     * Sauvegarde la session dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE sessions SET
                    id_partie = :id_partie,
                    id_lieu = :id_lieu,
                    date_session = :date_session,
                    heure_debut = :heure_debut,
                    heure_fin = :heure_fin,
                    id_maitre_jeu = :id_maitre_jeu,
                    max_joueurs = :max_joueurs
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_partie' => $this->idPartie,
                'id_lieu' => $this->idLieu,
                'date_session' => $this->dateSession,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'max_joueurs' => $this->maxJoueurs,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO sessions (
                    id_partie, id_lieu, date_session, heure_debut, heure_fin, id_maitre_jeu, max_joueurs
                )
                VALUES (
                    :id_partie, :id_lieu, :date_session, :heure_debut, :heure_fin, :id_maitre_jeu, :max_joueurs
                )
            ');
            $stmt->execute([
                'id_partie' => $this->idPartie,
                'id_lieu' => $this->idLieu,
                'date_session' => $this->dateSession,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'max_joueurs' => $this->maxJoueurs,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }

        // Invalider le cache pour la partie
        if (self::$cacheEnabled && $this->idPartie !== null) {
            $this->invalidateCache($this->idPartie);
        }
    }

    /**
     * Supprime la session de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer une session sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = :id');
        $result = $stmt->execute(['id' => $id]);

        // Invalider le cache pour la partie
        if (self::$cacheEnabled && $this->idPartie !== null) {
            $this->invalidateCache($this->idPartie);
        }

        return $result;
    }

    /**
     * Recherche des sessions avec filtres optionnels.
     *
     * @param PDO $pdo Instance PDO
     * @param int $partieId ID de la partie (optionnel)
     * @param int $lieuId ID du lieu (optionnel)
     * @param string $dateDebut Date de début (format Y-m-d, optionnel)
     * @param string $dateFin Date de fin (format Y-m-d, optionnel)
     * @param int|null $maxJoueurs Nombre maximum de joueurs (optionnel)
     * @return array Liste des sessions
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(
        PDO $pdo,
        int $partieId = 0,
        int $lieuId = 0,
        string $dateDebut = '',
        string $dateFin = '',
        ?int $maxJoueurs = null
    ): array {
        // Générer une clé de cache unique basée sur les paramètres
        $cacheKey = self::$cachePrefix . md5(serialize([$partieId, $lieuId, $dateDebut, $dateFin, $maxJoueurs]));

        // Vérifier le cache
        if (self::$cacheEnabled && extension_loaded('apcu')) {
            $cachedResult = apcu_fetch($cacheKey);
            if ($cachedResult !== false) {
                return $cachedResult;
            }
        }

        $sql = 'SELECT id, id_partie, id_lieu, date_session, heure_debut, heure_fin, id_maitre_jeu, max_joueurs FROM sessions WHERE 1=1';
        $params = [];

        if ($partieId > 0) {
            $sql .= ' AND id_partie = :partie_id';
            $params['partie_id'] = $partieId;
        }
        if ($lieuId > 0) {
            $sql .= ' AND id_lieu = :lieu_id';
            $params['lieu_id'] = $lieuId;
        }
        if ($dateDebut !== '' && isValidDate($dateDebut)) {
            $sql .= ' AND date_session >= :date_debut';
            $params['date_debut'] = $dateDebut;
        }
        if ($dateFin !== '' && isValidDate($dateFin)) {
            $sql .= ' AND date_session <= :date_fin';
            $params['date_fin'] = $dateFin;
        }
        if ($maxJoueurs !== null && $maxJoueurs >= 0) {
            $sql .= ' AND max_joueurs = :max_joueurs';
            $params['max_joueurs'] = $maxJoueurs;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stocker dans le cache
        if (self::$cacheEnabled && extension_loaded('apcu') && $partieId > 0) {
            apcu_store($cacheKey, $results, self::$cacheTTL);
        }

        return $results;
    }

    /**
     * Invalide le cache pour une partie donnée.
     *
     * @param int $partieId ID de la partie
     */
    private function invalidateCache(int $partieId): void
    {
        if (!extension_loaded('apcu')) {
            return;
        }
        // Invalider toutes les clés commençant par le préfixe et l'ID de la partie
        $prefix = self::$cachePrefix . md5(serialize([$partieId]));
        $cacheInfo = apcu_cache_info();
        foreach ($cacheInfo['cache_list'] as $entry) {
            if (strpos($entry['info'], $prefix) === 0) {
                apcu_delete($entry['info']);
            }
        }
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getPartie(): ?Partie
    {
        if ($this->partie === null && $this->idPartie !== null) {
            try {
                $this->partie = new Partie($this->idPartie);
            } catch (PDOException) {
                $this->idPartie = null;
            }
        }
        return $this->partie;
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

    public function getDateSession(): string
    {
        return $this->dateSession;
    }

    public function getHeureDebut(): string
    {
        return $this->heureDebut;
    }

    public function getHeureFin(): string
    {
        return $this->heureFin;
    }

    public function getMaitreJeu(): ?Utilisateur
    {
        if ($this->maitreJeu === null && $this->idMaitreJeu !== null) {
            try {
                $this->maitreJeu = new Utilisateur($this->idMaitreJeu);
            } catch (PDOException) {
                $this->idMaitreJeu = null;
            }
        }
        return $this->maitreJeu;
    }

    public function getMaxJoueurs(): ?int
    {
        return $this->maxJoueurs;
    }

    public function getNombreJoueursInscrits(): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM joueurs_session WHERE id_session = :id_session');
        $stmt->execute(['id_session' => $this->id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Récupère la liste des inscriptions (JoueursSession) associées à cette session.
     *
     * @return JoueursSession[]
     */
    public function getJoueursSession(): array
    {
        $inscriptions = [];

        $stmt = $this->pdo->prepare('
            SELECT js.id_session, js.id_utilisateur, js.date_inscription
            FROM joueurs_session js
            WHERE js.id_session = :id_session
        ');
        $stmt->execute(['id_session' => $this->id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $inscription = new JoueursSession(
                idSession: (int)$result['id_session'],
                idUtilisateur: $result['id_utilisateur']
            );
            $inscription->setDateInscription($result['date_inscription']);
            $inscriptions[] = $inscription;
        }

        return $inscriptions;
    }

    /**
     * Ajoute un joueur à la session.
     *
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @return JoueursSession
     * @throws InvalidArgumentException Si le nombre maximum de joueurs est atteint
     * @throws PDOException En cas d'erreur SQL ou si le joueur est déjà inscrit
     */
    public function ajouterJoueur(Utilisateur|string $utilisateurOuId): JoueursSession
    {
        if ($this->maxJoueurs !== null && $this->maxJoueurs > 0 && $this->getNombreJoueursInscrits() >= $this->maxJoueurs) {
            throw new InvalidArgumentException('Le nombre maximum de joueurs pour cette session est déjà atteint.');
        }
        $inscription = new JoueursSession(null, null, $this, $utilisateurOuId);
        $inscription->save();
        return $inscription;
    }

    /**
     * Retire un joueur de la session.
     *
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @throws InvalidArgumentException Si l'ID utilisateur est invalide
     * @throws PDOException Si l'inscription n'existe pas
     */
    public function retirerJoueur(Utilisateur|string $utilisateurOuId): void
    {
        $idUtilisateur = $utilisateurOuId instanceof Utilisateur ? $utilisateurOuId->getId() : $utilisateurOuId;

        if (!isValidUuid($idUtilisateur)) {
            throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
        }

        $inscription = new JoueursSession($this->id, $idUtilisateur);
        $inscription->delete();
    }

    /**
     * Vérifie si un joueur est inscrit à la session.
     *
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @return bool
     * @throws InvalidArgumentException Si l'ID utilisateur est invalide
     */
    public function estJoueur(Utilisateur|string $utilisateurOuId): bool
    {
        $idUtilisateur = $utilisateurOuId instanceof Utilisateur ? $utilisateurOuId->getId() : $utilisateurOuId;

        if (!isValidUuid($idUtilisateur)) {
            throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM joueurs_session WHERE id_session = :id_session AND id_utilisateur = :id_utilisateur');
        $stmt->execute([
            'id_session' => $this->id,
            'id_utilisateur' => $idUtilisateur,
        ]);

        return $stmt->fetchColumn() > 0;
    }

    // Setters

    public function setPartie(Partie|int $partie): self
    {
        if ($partie instanceof Partie) {
            $this->partie = $partie;
            $this->idPartie = $partie->getId();
        } else {
            $this->idPartie = $partie;
            $this->partie = null; // Lazy loading
        }
        return $this;
    }

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

    public function setDateSession(string $dateSession): self
    {
        if (!isValidDate($dateSession)) {
            throw new InvalidArgumentException('La date de session doit être au format Y-m-d.');
        }
        $this->dateSession = $dateSession;
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
        if ($this->heureDebut && $this->dateSession) {
            $debut = DateTime::createFromFormat('Y-m-d H:i:s', $this->dateSession . ' ' . $this->heureDebut);
            $fin = DateTime::createFromFormat('Y-m-d H:i:s', $this->dateSession . ' ' . $heureFin);
            if ($fin <= $debut) {
                throw new InvalidArgumentException('L\'heure de fin doit être postérieure à l\'heure de début.');
            }
        }
        $this->heureFin = $heureFin;
        return $this;
    }

    public function setMaitreJeu(Utilisateur|string $maitreJeu): self
    {
        if ($maitreJeu instanceof Utilisateur) {
            $this->maitreJeu = $maitreJeu;
            $this->idMaitreJeu = $maitreJeu->getId();
        } else {
            if (!isValidUuid($maitreJeu)) {
                throw new InvalidArgumentException('L\'ID du maître du jeu doit être un UUID valide.');
            }
            $this->idMaitreJeu = $maitreJeu;
            $this->maitreJeu = null; // Lazy loading
        }
        return $this;
    }

    public function setMaxJoueurs(?int $maxJoueurs): self
    {
        if ($maxJoueurs !== null && $maxJoueurs < 0) {
            throw new InvalidArgumentException('Le nombre maximum de joueurs ne peut pas être négatif.');
        }
        $this->maxJoueurs = $maxJoueurs;
        return $this;
    }

    // Helper Methods

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'id_partie' => $this->idPartie,
            'id_lieu' => $this->idLieu,
            'partie' => $this->getPartie(),
            'lieu' => $this->getLieu(),
            'date_session' => $this->getDateSession(),
            'heure_debut' => $this->getHeureDebut(),
            'heure_fin' => $this->getHeureFin(),
            'id_maitre_jeu' => $this->idMaitreJeu,
            'maitre_jeu' => $this->getMaitreJeu(),
            'max_joueurs' => $this->getMaxJoueurs(),
        ];
    }
}