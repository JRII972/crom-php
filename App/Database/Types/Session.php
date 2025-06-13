<?php

declare(strict_types=1);

namespace App\Database\Types;

use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Enumération pour l'état d'une session.
 */
enum EtatSession: string
{
    case Ouverte = 'OUVERTE';
    case Fermer = 'FERMER';
    case Annuler = 'ANNULER';
    case Supprimer = 'SUPPRIMER';
    case Complete = 'COMPLETE';
}

//FIXME: add max_joueurs_session

/**
 * Classe représentant une session dans la base de données.
 */
class Session extends DefaultDatabaseType
{    private ?int $idActivite;
    private ?Activite $activite = null;
    private ?int $idLieu;
    private ?Lieu $lieu = null;
    private string $nom;
    private EtatSession $etat = EtatSession::Ouverte;
    private string $dateSession;
    private string $heureDebut;
    private string $heureFin;
    private ?string $idMaitreJeu;
    private ?Utilisateur $maitreJeu = null;
    private ?int $maxJoueurs = null;
    private int $maxJoueursSession = 5; // Ajout de la propriété avec valeur par défaut

    // Cache configuration
    private static $cacheEnabled = false; // Activer/désactiver le cache
    private static $cacheTTL = 300; // 5 minutes en secondes
    private static $cachePrefix = 'session_search_';    /**
     * Constructeur de la classe Session.
     *
     * @param int|null $id Identifiant de la session (si fourni, charge depuis la base)
     * @param Activite|int|null $activiteOuId Objet Activite ou ID de la activite (requis si $id est null)
     * @param Lieu|int|null $lieuOuId Objet Lieu ou ID du lieu (requis si $id est null)
     * @param string|null $nom Nom de la session (optionnel, défaut "Session")
     * @param string|null $dateSession Date de la session (format Y-m-d, requis si $id est null)
     * @param string|null $heureDebut Heure de début (format H:i:s, requis si $id est null)
     * @param string|null $heureFin Heure de fin (format H:i:s, requis si $id est null)
     * @param Utilisateur|string|null $maitreJeuOuId Objet Utilisateur ou ID du maître du jeu (requis si $id est null)
     * @param int|null $maxJoueurs Nombre maximum de joueurs pour la session (optionnel)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si la session n'existe pas dans la base
     */public function __construct(
        ?int $id = null,
        Activite|int|null $activiteOuId = null,
        Lieu|int|null $lieuOuId = null,
        ?string $nom = null,
        ?string $dateSession = null,
        ?string $heureDebut = null,
        ?string $heureFin = null,
        Utilisateur|string|null $maitreJeuOuId = null,
        ?int $maxJoueurs = null,
        ?int $maxJoueursSession = null // Ajout du paramètre
    ) {
        parent::__construct();
        $this->table = 'sessions';        if ($id !== null && $activiteOuId === null && $lieuOuId === null && $nom === null && $dateSession === null && 
            $heureDebut === null && $heureFin === null && $maitreJeuOuId === null && $maxJoueurs === null && $maxJoueursSession === null) {
            // Mode : Charger la session depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $activiteOuId !== null && $lieuOuId !== null && $dateSession !== null && 
                  $heureDebut !== null && $heureFin !== null && $maitreJeuOuId !== null) {
            // Mode : Créer une nouvelle session
            $this->setActivite($activiteOuId);
            $this->setLieu($lieuOuId);
            $this->setNom($nom ?? 'Session'); // Valeur par défaut si nom non fourni
            $this->setDateSession($dateSession);
            $this->setHeureDebut($heureDebut);
            $this->setHeureFin($heureFin);
            $this->setMaitreJeu($maitreJeuOuId);
            // Définir maxJoueurs par défaut
            if ($maxJoueurs === null && $this->activite !== null) {
                if ($this->activite->getMaxJoueursSession() !== null) {
                    $this->setMaxJoueurs($this->activite->getMaxJoueursSession());
                } else {
                    $this->setMaxJoueurs($this->activite->getNombreMaxJoueurs());
                }
            } else {
                $this->setMaxJoueurs($maxJoueurs);
            }
            // Gestion de maxJoueursSession
            if ($maxJoueursSession !== null) {
                $this->setMaxJoueursSession($maxJoueursSession);
            } elseif ($maxJoueurs !== null && $maxJoueurs < 6) {
                $this->setMaxJoueursSession($maxJoueurs);
            } else {
                $this->setMaxJoueursSession(5);
            }
        } else {
            throw new InvalidArgumentException('Trop d\'argument on été fournis, soit ID seul, soit les donnée...'); //TODO: reformuler
        }
    }

    /**
     * Charge les données de la session depuis la base de données.
     *
     * @param int $id Identifiant de la session
     * @throws PDOException Si la session n'existe pas
     */    
    private function loadFromDatabase(int|null $id): void
    {
        if ($id === null) {
            $id = $this->id;
        }
        $stmt = $this->pdo->prepare('SELECT * FROM sessions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Session non trouvée pour l\'ID : ' . $id);
        }

        $this->updateFromDatabaseData($data);
    }

    private function updateFromDatabaseData(array $data): void
    {
        $this->id = (int) $data['id'];
        $this->idActivite = (int) $data['id_activite'];
        $this->idLieu = (int) $data['id_lieu'];
        $this->nom = $data['nom'] ?? 'Session'; // Valeur par défaut si colonne nom n'existe pas
        $this->etat = EtatSession::from($data['etat']);
        $this->dateSession = $data['date_session'];
        $this->heureDebut = $data['heure_debut'];
        $this->heureFin = $data['heure_fin'];
        $this->idMaitreJeu = $data['id_maitre_jeu'];
        $this->maxJoueurs = isset($data['nombre_max_joueurs']) ? (int) $data['nombre_max_joueurs'] : null;
        $this->maxJoueursSession = isset($data['max_joueurs_session']) ? (int) $data['max_joueurs_session'] : 5;
    }

    /**
     * Sauvegarde la session dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE sessions SET
                    id_activite = :id_activite,
                    id_lieu = :id_lieu,
                    nom = :nom,
                    etat = :etat,
                    date_session = :date_session,
                    heure_debut = :heure_debut,
                    heure_fin = :heure_fin,
                    id_maitre_jeu = :id_maitre_jeu,
                    nombre_max_joueurs = :nombre_max_joueurs,
                    max_joueurs_session = :max_joueurs_session
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'id_activite' => $this->idActivite,
                'id_lieu' => $this->idLieu,
                'nom' => $this->nom,
                'etat' => $this->etat->value,
                'date_session' => $this->dateSession,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'nombre_max_joueurs' => $this->maxJoueurs,
                'max_joueurs_session' => $this->maxJoueursSession,
            ]);
        } else {            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO sessions (
                    id_activite, id_lieu, nom, etat, date_session, heure_debut, heure_fin, id_maitre_jeu, nombre_max_joueurs, max_joueurs_session
                )
                VALUES (
                    :id_activite, :id_lieu, :nom, :etat, :date_session, :heure_debut, :heure_fin, :id_maitre_jeu, :nombre_max_joueurs, :max_joueurs_session
                )
            ');
            $stmt->execute([
                'id_activite' => $this->idActivite,
                'id_lieu' => $this->idLieu,
                'nom' => $this->nom,
                'etat' => $this->etat->value,
                'date_session' => $this->dateSession,
                'heure_debut' => $this->heureDebut,
                'heure_fin' => $this->heureFin,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'nombre_max_joueurs' => $this->maxJoueurs,
                'max_joueurs_session' => $this->maxJoueursSession,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }

        // Invalider le cache pour la activite
        if (self::$cacheEnabled && $this->idActivite !== null) {
            $this->invalidateCache();
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
        $result = $stmt->execute(['id' => $this->id]);

        // Invalider le cache pour la activite
        if (self::$cacheEnabled && $this->idActivite !== null) {
            $this->invalidateCache();
        }

        return $result;
    }    
    
    /**
     * Recherche des sessions avec filtres optionnels.
     *
     * @param PDO $pdo Instance PDO
     * @param int $activiteId ID de la activite (optionnel)
     * @param int $lieuId ID du lieu (optionnel)
     * @param string $dateDebut Date de début (format Y-m-d, optionnel)
     * @param string $dateFin Date de fin (format Y-m-d, optionnel)
     * @param int|null $maxJoueurs Nombre maximum de joueurs (optionnel)
     * @param array|null $categories Liste des catégories/genres pour filtrer (optionnel)
     * @param array|null $jours Liste des jours de la semaine pour filtrer (optionnel)
     * @param bool|null $serialize 
     * @return array Liste des sessions
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(
        PDO $pdo,
        ?int $activiteId = 0,
        ?int $lieuId = 0,
        ?string $dateDebut = '',
        ?string $dateFin = '',
        ?int $maxJoueurs = null,
        ?array $categories = null,
        ?array $jours = null,
        ?bool $serialize = true
    ): array {
        // Générer une clé de cache unique basée sur les paramètres
        $cacheKey = self::$cachePrefix . md5(serialize([$activiteId, $lieuId, $dateDebut, $dateFin, $maxJoueurs, $categories, $jours]));

        // Vérifier le cache (toujours, même si activiteId = 0)
        if (self::$cacheEnabled && extension_loaded('apcu')) {
            $cachedResult = apcu_fetch($cacheKey);
            if ($cachedResult !== false) {
                return $cachedResult;
            }
        }

        // Base SQL query
        $sql = 'SELECT DISTINCT s.* FROM sessions s';
        $params = [];
        
        // Ajouter les jointures pour filtrer par catégories/genres si nécessaire
        if ($categories !== null && !empty($categories)) {
            $sql .= ' JOIN activites p ON s.id_activite = p.id';
            $sql .= ' JOIN jeux j ON p.id_jeu = j.id';
            $sql .= ' JOIN jeux_genres jg ON j.id = jg.id_jeu';
            $sql .= ' JOIN genres g ON jg.id_genre = g.id';
            $placeholders = [];
            foreach ($categories as $key => $value) {
                // Si c'est un objet Genre, on prend son id, sinon on prend la valeur brute
                if (is_object($value) && method_exists($value, 'getId')) {
                    $paramValue = $value->getId();
                } else if (is_array($value) && isset($value['id'])) {
                    $paramValue = $value['id'];
                } else {
                    $paramValue = $value;
                }
                $paramName = 'category_' . $key;
                $placeholders[] = ':' . $paramName;
                $params[$paramName] = $paramValue;
            }
            $sql .= ' AND g.id IN (' . implode(', ', $placeholders) . ')';
        }
        
        $sql .= ' WHERE 1=1';

        if ($activiteId > 0) {
            $sql .= ' AND s.id_activite = :activite_id';
            $params['activite_id'] = $activiteId;
        }        
        if ($lieuId > 0) {
            $sql .= ' AND s.id_lieu = :lieu_id';
            $params['lieu_id'] = $lieuId;
        }
        if ($dateDebut !== '' && isValidDate($dateDebut)) {
            $sql .= ' AND s.date_session >= :date_debut';
            $params['date_debut'] = $dateDebut;
        }
        if ($dateFin !== '' && isValidDate($dateFin)) {
            $sql .= ' AND s.date_session <= :date_fin';
            $params['date_fin'] = $dateFin;
        }
        if ($maxJoueurs !== null && $maxJoueurs >= 0) {
            $sql .= ' AND s.max_joueurs_session <= :nombre_max_joueurs';
            $params['nombre_max_joueurs'] = $maxJoueurs;
        }
        
        // Filtre par jours de la semaine
        if ($jours !== null && !empty($jours)) {
            $joursFiltres = [];
            foreach ($jours as $key => $jour) {
                $paramName = 'jour_' . $key;
                $joursFiltres[] = "DAYOFWEEK(s.date_session) = :" . $paramName;
                
                // Conversion du nom du jour en nombre (DAYOFWEEK retourne 1=Dimanche, 2=Lundi, etc.)
                $jourMap = [
                    'dimanche' => 1, 'lundi' => 2, 'mardi' => 3, 'mercredi' => 4,
                    'jeudi' => 5, 'vendredi' => 6, 'samedi' => 7,
                    'sunday' => 1, 'monday' => 2, 'tuesday' => 3, 'wednesday' => 4,
                    'thursday' => 5, 'friday' => 6, 'saturday' => 7,
                    '0' => 1, '1' => 2, '2' => 3, '3' => 4, '4' => 5, '5' => 6, '6' => 7
                ];
                
                $jour = strtolower($jour);
                $params[$paramName] = isset($jourMap[$jour]) ? $jourMap[$jour] : (is_numeric($jour) ? ((int)$jour % 7) + 1 : 0);
            }
            
            if (!empty($joursFiltres)) {
                $sql .= ' AND (' . implode(' OR ', $joursFiltres) . ')';
            }
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Utilise un constructeur statique pour créer les objets
        $sessions = [];
        foreach ($results as $row) {
            try {
                $session = new self((int)$row['id']);
                if ( $serialize ) {
                    $sessions[] = $session->jsonSerialize();
                } else {
                    $sessions[] = $session;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Stocker dans le cache (toujours, même si activiteId = 0)
        if (self::$cacheEnabled && extension_loaded('apcu') && $serialize) {
            apcu_store($cacheKey, $sessions, self::$cacheTTL);
        }

        return $sessions;
    }

    //TODO: deporter certaine recherche pour filtre option
    /**
     * Récupère les options de filtres disponibles pour les sessions.
     * 
     * @param PDO $pdo Instance PDO
     * @return array Tableau associatif des options de filtres
     * @throws PDOException En cas d'erreur SQL
     */
    public static function getFilterOptions(PDO $pdo): array
    {
        $options = [];
        
        // Récupération de la liste des lieux
        $stmtLieux = $pdo->prepare('SELECT id, nom FROM lieux ORDER BY nom');
        $stmtLieux->execute();
        $options['lieux'] = $stmtLieux->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupération de la date de début la plus ancienne
        $stmtDateDebut = $pdo->prepare('SELECT MAX(date_session) as date_debut FROM sessions');
        $stmtDateDebut->execute();
        $dateDebut = $stmtDateDebut->fetch(PDO::FETCH_ASSOC);
        $options['date_debut'] = $dateDebut['date_debut'] ?? date('Y-m-d');
        
        // Récupération de la date de fin la plus récente
        $stmtDateFin = $pdo->prepare('SELECT MIN(date_session) as date_fin FROM sessions');
        $stmtDateFin->execute();
        $dateFin = $stmtDateFin->fetch(PDO::FETCH_ASSOC);
        $options['date_fin'] = $dateFin['date_fin'] ?? date('Y-m-d', strtotime('+1 year'));
        
        // Récupération du nombre maximum de joueurs
        $stmtMaxJoueurs = $pdo->prepare('
            SELECT GREATEST(
                COALESCE(MAX(s.nombre_max_joueurs), 0),
                COALESCE(MAX(p.nombre_max_joueurs), 0)
            ) as max_joueurs
            FROM sessions s
            LEFT JOIN activites p ON s.id_activite = p.id
        ');
        $stmtMaxJoueurs->execute();
        $maxJoueurs = $stmtMaxJoueurs->fetch(PDO::FETCH_ASSOC);
        $options['max_joueurs'] = (int)($maxJoueurs['max_joueurs'] ?? 10);
        
        // Liste des jours de la semaine
        $options['jours'] = [
            1 => 'Dimanche',
            2 => 'Lundi',
            3 => 'Mardi',
            4 => 'Mercredi',
            5 => 'Jeudi',
            6 => 'Vendredi',
            7 => 'Samedi'
        ];
        
        // Récupération de la liste des catégories (genres)
        $stmtCategories = $pdo->prepare('SELECT id, nom FROM genres ORDER BY nom');
        $stmtCategories->execute();
        $options['categories'] = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
        
        return $options;
    }

    /**
     * Invalide le cache pour une activite donnée OU pour tout filtre général qui inclurait cette session.
     */
    private function invalidateCache(): void
    {
        if (!extension_loaded('apcu')) {
            return;
        }
        $cacheInfo = apcu_cache_info();
        if (!isset($cacheInfo['cache_list'])) {
            return;
        }

        // On invalide tous les caches qui pourraient contenir cette session :
        // - ceux filtrés sur cette activite
        // - ceux filtrés sur ce lieu
        // - ceux filtrés sur la date de la session
        // - ceux généraux (aucun filtre)
        foreach ($cacheInfo['cache_list'] as $entry) {
            if (!isset($entry['info'])) continue;
            $key = $entry['info'];
            // On décode la clé pour retrouver les paramètres du filtre
            if (str_starts_with($key, self::$cachePrefix)) {
                // On essaie de retrouver les paramètres du filtre à partir du hash
                // Comme on ne peut pas inverser le hash, on invalide tout cache qui commence par le préfixe (stratégie simple)
                apcu_delete($key);
            }
        }
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getActivite(): ?Activite
    {
        if ($this->activite === null && $this->idActivite !== null) {
            try {
                $this->activite = new Activite($this->idActivite);
            } catch (PDOException) {
                $this->idActivite = null;
            }
        }
        return $this->activite;
    }    public function getLieu(): ?Lieu
    {
        if ($this->lieu === null && $this->idLieu !== null) {
            try {
                $this->lieu = new Lieu($this->idLieu);
            } catch (PDOException) {
                $this->idLieu = null;
            }
        }
        return $this->lieu;
    }    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEtat(): EtatSession
    {
        return $this->etat;
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
    }    public function getNombreJoueursInscrits(): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM joueurs_session WHERE id_session = :id_session');
        $stmt->execute(['id_session' => $this->id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne le numéro de la session par rapport à sa activite.
     * Filtre toutes les sessions de la activite, les trie par date et retourne le rang de cette session.
     *
     * @return int Le numéro de la session (commence à 1)
     * @throws InvalidArgumentException Si l'ID de la session ou de la activite n'est pas défini
     */
    public function getSessionNumber(): int
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('L\'ID de la session doit être défini pour calculer son numéro.');
        }
        
        if (!isset($this->idActivite)) {
            throw new InvalidArgumentException('L\'ID de la activite doit être défini pour calculer le numéro de session.');
        }

        // Récupérer toutes les sessions de la activite triées par date et heure
        $stmt = $this->pdo->prepare('
            SELECT id 
            FROM sessions 
            WHERE id_activite = :id_activite 
            ORDER BY date_session ASC, heure_debut ASC
        ');
        $stmt->execute(['id_activite' => $this->idActivite]);
        $sessionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Trouver le rang de cette session dans la liste
        $sessionNumber = array_search($this->id, $sessionIds);
        
        // array_search retourne false si non trouvé, sinon l'index (commence à 0)
        if ($sessionNumber === false) {
            throw new InvalidArgumentException('Cette session n\'a pas été trouvée dans la activite associée.');
        }

        // Retourner le numéro de session (commence à 1)
        return $sessionNumber + 1;
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

    // Vérifie si l'utilisateur est inscrit à la session
    public function estInscrit(Utilisateur|string|null $utilisateurOuId): bool
    {
        if (is_null($utilisateurOuId)){
            return false;
        }
        $id = $utilisateurOuId instanceof Utilisateur ? $utilisateurOuId->getId() : $utilisateurOuId;
        foreach ($this->getJoueursSession() as $inscription) {
            if (method_exists($inscription, 'getUtilisateur')) {
                $joueur = $inscription->getUtilisateur();
                if ($joueur instanceof Utilisateur && $joueur->getId() === $id) {
                    return true;
                }
            } elseif (method_exists($inscription, 'getIdUtilisateur')) {
                if ($inscription->getIdUtilisateur() === $id) {
                    return true;
                }
            }
        }
        return false;
    }

    // Setters

    public function setActivite(Activite|int $activite): self
    {
        if ($activite instanceof Activite) {
            $this->activite = $activite;
            $this->idActivite = $activite->getId();
        } else {
            $this->idActivite = $activite;
            $this->activite = null; // Lazy loading
        }
        return $this;
    }    public function setLieu(Lieu|int $lieu): self
    {
        if ($lieu instanceof Lieu) {
            $this->lieu = $lieu;
            $this->idLieu = $lieu->getId();
        } else {
            $this->idLieu = $lieu;
            $this->lieu = null; // Lazy loading
        }
        return $this;
    }    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom de la session ne peut pas être vide.');
        }
        $this->nom = trim($nom);
        return $this;
    }

    public function setEtat(EtatSession $etat): self
    {
        $this->etat = $etat;
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

    // Getter et setter pour maxJoueursSession

    public function getMaxJoueursSession(): int
    {
        return $this->maxJoueursSession;
    }

    public function setMaxJoueursSession(int $maxJoueursSession): self
    {
        if ($maxJoueursSession < 1) {
            throw new InvalidArgumentException('max_joueurs_session doit être supérieur à 0.');
        }
        $this->maxJoueursSession = $maxJoueursSession;
        return $this;    }

    // Helper Methods

    public function isLocked():bool {
        // TODO: ajouter support de l'etat de l'activite pour definir le verouillage de la session
        return ($this->getMaxJoueurs() <= $this->getNombreJoueursInscrits()) || ($this->getEtat() == EtatSession::Complete) || ($this->getEtat() == EtatSession::Fermer) || ($this->getEtat() == EtatSession::Supprimer);
    }

    public function jsonSerialize(): array
    {        return [
            'id' => $this->getId(),
            'id_activite' => $this->idActivite,
            'id_lieu' => $this->idLieu,
            'nom' => $this->getNom(),
            'etat' => $this->getEtat()->value,
            'activite' => $this->getActivite()->jsonSerialize(),
            'lieu' => $this->getLieu()->jsonSerialize(),
            'date_session' => $this->getDateSession(),
            'heure_debut' => $this->getHeureDebut(),
            'heure_fin' => $this->getHeureFin(),
            'id_maitre_jeu' => $this->idMaitreJeu,
            'maitre_jeu' => $this->getMaitreJeu()->jsonSerialize(),
            'nombre_max_joueurs' => $this->getMaxJoueurs(),
            'max_joueurs_session' => $this->getMaxJoueursSession(),
            'nombre_joueurs_session' => $this->getNombreJoueursInscrits(),            'joueurs_session' => array_map(
                fn($js) => $js->jsonSerialize(),
                $this->getJoueursSession()
            ),
        ];
    }

}