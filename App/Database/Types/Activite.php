<?php

declare(strict_types=1);

namespace App\Database\Types;

use App\Utils\Image;
use InvalidArgumentException;
use PDO;
use DateTime;
use PDOException;

require_once __DIR__ . '/../../Utils/helpers.php';

/**
 * Enumération pour le type de activite.
 */
enum TypeActivite: string
{
    case Campagne = 'CAMPAGNE';
    case Oneshot = 'ONESHOT';
    case JeuDeSociete = 'JEU_DE_SOCIETE';
    case Evenement = 'EVENEMENT';
}

/**
 * Enumération pour le type de campagne.
 */
enum TypeCampagne: string
{
    case Ouverte = 'OUVERTE';
    case Fermee = 'FERMEE';
}

/**
 * Enumération pour l'état d'une activité.
 */
enum EtatActivite: string
{
    case Active = 'ACTIVE';
    case Fermer = 'FERMER';
    case Terminer = 'TERMINER';
    case Annuler = 'ANNULER';
    case Supprimer = 'SUPPRIMER';
}

/**
 * Classe représentant une activite dans la base de données.
 */
class Activite extends DefaultDatabaseType
{    protected ?int $idJeu;
    private ?string $nom;
    private ?Jeu $jeu = null;
    protected ?string $idMaitreJeu;
    private ?Utilisateur $maitreJeu = null;
    private EtatActivite $etat = EtatActivite::Active;
    private TypeActivite $typeActivite;
    private ?TypeCampagne $typeCampagne = null;
    private ?string $descriptionCourte = null;
    private ?string $description = null;
    private int $nombreMaxJoueurs = 0;
    private int $maxJoueursSession = 5;
    private bool $verrouille = false;
    private ?Image $image = null;
    private ?string $texteAltImage = null;
    private string $dateCreation;
    
    // Cache configuration
    private static $cacheEnabled = false; // Activer/désactiver le cache
    private static $cacheTTL = 300; // 5 minutes en secondes
    private static $cachePrefix = 'activite_search_';

    /**
     * Constructeur de la classe Activite.
     *
     * @param int|null $id Identifiant de la activite (si fourni, charge depuis la base)
     * @param string|null $nom Nom de la Activite
     * @param Jeu|int|null $jeuOuId Objet Jeu ou ID du jeu (requis si $id est null)
     * @param Utilisateur|string|null $maitreJeuOuId Objet Utilisateur ou ID du maître du jeu (requis si $id est null)
     * @param TypeActivite|null $typeActivite Type de activite (requis si $id est null)
     * @param TypeCampagne|null $typeCampagne Type de campagne
     * @param string|null $descriptionCourte Description courte
     * @param string|null $description Description complète
     * @param int|null $nombreMaxJoueurs Nombre maximum de joueurs
     * @param int|null $maxJoueursSession Nombre maximum de joueurs par session
     * @param Image|string|array|null $image Image de la activite
     * @param string|null $texteAltImage Texte alternatif de l'image
     * @param string|null $dateCreation Date de création (format Y-m-d H:i:s)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si la activite n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        Jeu|int|null $jeuOuId = null,
        Utilisateur|string|null $maitreJeuOuId = null,
        ?TypeActivite $typeActivite = null,
        ?TypeCampagne $typeCampagne = null,
        ?string $descriptionCourte = null,
        ?string $description = null,
        ?int $nombreMaxJoueurs = null,
        ?int $maxJoueursSession = null,
        Image|string|array|null $image = null,
        ?string $texteAltImage = null,
        ?string $dateCreation = null
    ) {
        parent::__construct();
        $this->table = 'activites';
        
        if ($id !== null) {
            // Mode : Charger la activite depuis la base
            $this->loadFromDatabase($id);
        } elseif ($nom !== null && $jeuOuId !== null && $maitreJeuOuId !== null && $typeActivite !== null) {
            // Mode : Créer une nouvelle activite
            $this->setNom($nom);
            $this->setJeu($jeuOuId);
            $this->setMaitreJeu($maitreJeuOuId);
            $this->setTypeActivite($typeActivite);
            if ($typeCampagne !== null) {
                $this->setTypeCampagne($typeCampagne);
            } elseif ($typeActivite == TypeActivite::Campagne) {
                $this->setTypeCampagne(TypeCampagne::Ouverte);
            }
            if ($descriptionCourte !== null) {
                $this->setDescriptionCourte($descriptionCourte);
            }
            if ($description !== null) {
                $this->setDescription($description);
            }
            if ($nombreMaxJoueurs !== null) {
                $this->setNombreMaxJoueurs($nombreMaxJoueurs);
            }
            if ($image !== null) {
                $this->image = Image::load($image);
            }
            if ($texteAltImage !== null) {
                $this->setTexteAltImage($texteAltImage);
            }
            if ($maxJoueursSession !== null) {
                $this->setMaxJoueursSession($maxJoueursSession);
            }
            $this->dateCreation = $dateCreation ?? date('Y-m-d H:i:s');
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit jeuOuId, maitreJeuOuId, et typeActivite ' .
                '(et éventuellement typeCampagne, descriptionCourte, description, nombreMaxJoueurs, urlImage, texteAltImage, dateCreation).'
            );
        }
    }

    /**
     * Charge les données de la activite depuis la base de données.
     *
     * @param int $id Identifiant de la activite
     * @throws PDOException Si la activite n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM activites WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Activite non trouvée pour l\'ID : ' . $id);
        }        $this->id = (int) $data['id'];
        $this->nom = $data['nom'];
        $this->etat = EtatActivite::from($data['etat']);
        $this->idJeu = (int) $data['id_jeu'];
        $this->idMaitreJeu = $data['id_maitre_jeu'];
        $this->typeActivite = TypeActivite::from($data['type_activite']);
        $this->typeCampagne = $data['type_campagne'] ? TypeCampagne::from($data['type_campagne']) : null;
        $this->descriptionCourte = $data['description_courte'];
        $this->description = $data['description'];
        $this->nombreMaxJoueurs = (int) $data['nombre_max_joueurs'];
        $this->maxJoueursSession = (int) $data['max_joueurs_session'];
        $this->verrouille = (bool) $data['verrouille'];        
        $this->image = Image::load($data['image']);
        $this->texteAltImage = $data['texte_alt_image'];
        $this->dateCreation = $data['date_creation'];
    }

    /**
     * Sauvegarde la activite dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE activites SET
                    nom = :nom,
                    etat = :etat,
                    id_jeu = :id_jeu,
                    id_maitre_jeu = :id_maitre_jeu,
                    type_activite = :type_activite,
                    type_campagne = :type_campagne,
                    description_courte = :description_courte,
                    description = :description,
                    nombre_max_joueurs = :nombre_max_joueurs,
                    max_joueurs_session = :max_joueurs_session,
                    verrouille = :verrouille,
                    image = :image,
                    texte_alt_image = :texte_alt_image
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'nom' => $this->nom,
                'etat' => $this->etat->value,
                'id_jeu' => $this->idJeu,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'type_activite' => $this->typeActivite->value,
                'type_campagne' => $this->typeCampagne?->value,
                'description_courte' => $this->descriptionCourte,
                'description' => $this->description,
                'nombre_max_joueurs' => $this->nombreMaxJoueurs,
                'max_joueurs_session' => $this->maxJoueursSession,
                'verrouille' => $this->verrouille ? 1 : 0,
                'image' => $this->image ? $this->image->getFilePath() : null,
                'texte_alt_image' => $this->texteAltImage
            ]);
        } else {            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO activites (
                    nom, etat, id_jeu, id_maitre_jeu, type_activite, type_campagne, description_courte,
                    description, nombre_max_joueurs, max_joueurs_session, verrouille, image, texte_alt_image
                )
                VALUES (
                    :nom, :etat, :id_jeu, :id_maitre_jeu, :type_activite, :type_campagne, :description_courte,
                    :description, :nombre_max_joueurs, :max_joueurs_session, :verrouille, :image, :texte_alt_image
                )
            ');
            $stmt->execute([
                'nom' => $this->nom,
                'etat' => $this->etat->value,
                'id_jeu' => $this->idJeu,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'type_activite' => $this->typeActivite->value,
                'type_campagne' => $this->typeCampagne?->value,
                'description_courte' => $this->descriptionCourte,
                'description' => $this->description,
                'nombre_max_joueurs' => $this->nombreMaxJoueurs,
                'max_joueurs_session' => $this->maxJoueursSession,                'verrouille' => $this->verrouille ? 1 : 0,
                'image' => $this->image ? $this->image->getFilePath() : null,
                'texte_alt_image' => $this->texteAltImage,            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
        
        // Invalider le cache
        $this->invalidateCache();
    }

    /**
     * Supprime la activite de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer une activite sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM activites WHERE id = :id');
        $stmt->execute(['id' => $this->id]);        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucune activite supprimée : activite non trouvée.');
        }
        
        // Invalider le cache
        $this->invalidateCache();

        return true;
    }

    /**
     * Recherche des activites avec filtres optionnels.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé pour la recherche (optionnel)
     * @param int $idJeu ID du jeu (optionnel)
     * @param string $idMaitreJeu ID du maître du jeu (optionnel)
     * @param string $typeActivite Type de activite (optionnel, CAMPAGNE, ONESHOT, JEU_DE_SOCIETE, EVENEMENT)
     * @param array|null $categories Liste des catégories/genres pour filtrer (optionnel)
     * @param array|null $jours Liste des jours de la semaine pour filtrer les sessions associées (optionnel)
     * @return array Liste des activites
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(
        PDO $pdo, 
        string $keyword = '', 
        int $idJeu = 0, 
        string $idMaitreJeu = '', 
        string $typeActivite = '',
        ?array $categories = null,
        ?array $jours = null
    ): array {
        // Générer une clé de cache unique basée sur les paramètres
        $cacheKey = self::$cachePrefix . md5(serialize([$keyword, $idJeu, $idMaitreJeu, $typeActivite, $categories, $jours]));

        // Vérifier le cache
        if (self::$cacheEnabled && extension_loaded('apcu')) {
            $cachedResult = apcu_fetch($cacheKey);
            if ($cachedResult !== false) {
                return $cachedResult;
            }
        }
        
        // Base SQL query
        $sql = 'SELECT DISTINCT p.id, p.nom, p.id_jeu, p.id_maitre_jeu, p.type_activite, p.type_campagne, 
               p.description_courte, p.description, p.nombre_max_joueurs, p.max_joueurs_session, 
               p.verrouille, p.image, p.texte_alt_image, p.date_creation 
               FROM activites p';
        $params = [];

        // Ajouter les jointures nécessaires selon les filtres
        $activeFiltreCategorie = $categories !== null && !empty($categories);
        $needSessionsJoin = $jours !== null && !empty($jours);
        
        if ($activeFiltreCategorie) {
            $sql .= ' JOIN jeux j ON p.id_jeu = j.id';
            $sql .= ' JOIN jeux_genres jg ON j.id = jg.id_jeu';
            $sql .= ' JOIN genres g ON jg.id_genre = g.id';
        }
        
        if ($needSessionsJoin) {
            $sql .= ' LEFT JOIN sessions s ON p.id = s.id_activite';
        }
        
        $sql .= ' WHERE 1=1';

        if ($idJeu > 0) {
            $sql .= ' AND p.id_jeu = :id_jeu';
            $params['id_jeu'] = $idJeu;
        }
        if ($idMaitreJeu !== '') {
            $sql .= ' AND p.id_maitre_jeu = :id_maitre_jeu';
            $params['id_maitre_jeu'] = $idMaitreJeu;
        }
        if ($typeActivite !== '' && in_array($typeActivite, [
            TypeActivite::Campagne->value,
            TypeActivite::Oneshot->value,
            TypeActivite::JeuDeSociete->value,
            TypeActivite::Evenement->value
        ], true)) {
            $sql .= ' AND p.type_activite = :type_activite';
            $params['type_activite'] = $typeActivite;
        }
        if ($keyword !== '') {
            $sql .= ' AND (p.nom LIKE :keyword OR p.description LIKE :keyword)';
            $params['keyword'] = '%' . $keyword . '%';
        }

        // Filtre par catégories/genres
        if ($activeFiltreCategorie) {
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
        
        // Filtre par jours de la semaine des sessions
        if ($needSessionsJoin) {
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
        
        // Créer des objets Activite
        $activites = [];
        foreach ($results as $row) {
            try {
                $activite = new self((int)$row['id']);
                $activites[] = $activite->jsonSerialize();
            } catch (\Throwable $e) {
                continue;
            }
        }
        
        // Stocker dans le cache
        if (self::$cacheEnabled && extension_loaded('apcu')) {
            apcu_store($cacheKey, $activites, self::$cacheTTL);
        }

        return $activites;
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getJeu(): ?Jeu
    {
        if ($this->jeu === null && $this->idJeu !== null) {
            try {
                $this->jeu = new Jeu($this->idJeu);
            } catch (PDOException) {
                $this->idJeu = null;
            }
        }
        return $this->jeu;
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

    public function getTypeActivite(): TypeActivite
    {
        return $this->typeActivite;
    }    public function getTypeCampagne(): ?TypeCampagne
    {
        return $this->typeCampagne;
    }

    public function getEtat(): EtatActivite
    {
        return $this->etat;
    }

    public function getDescriptionCourte(): ?string
    {
        return $this->descriptionCourte;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getNombreMaxJoueurs(): int
    {
        return $this->nombreMaxJoueurs;
    }

    public function getMaxJoueursSession(): int
    {
        return $this->maxJoueursSession;
    }

    public function getVerrouille(): bool
    {
        return $this->verrouille;
    }

    public function getImage(): ?Image
    {
        if (!is_null($this->image) && !$this->image->isValid()){
            $this->image = null;
        }
        return $this->image;
    }

    public function getTexteAltImage(): ?string
    {
        return $this->texteAltImage;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    /**
     * Récupère la liste des membres de la activite.
     *
     * @return MembreActivite[]
     */
    public function getMembresActivite(): array
    {
        $membres = [];

        $stmt = $this->pdo->prepare('SELECT id_utilisateur FROM membres_activite WHERE id_activite = :id_activite');
        $stmt->execute(['id_activite' => $this->id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            try {
                $utilisateur = new Utilisateur($result['id_utilisateur']);
                $membres[] = $utilisateur; // À ajuster si MembreActivite est requis
            } catch (PDOException) {
                // Ignorer les utilisateurs non trouvés
            }
        }

        return $membres;
    }

    /**
     * Récupère la liste des sessions associées à la activite.
     *
     * @return Session[]
     */
    public function getSessions(): array
    {
        $sessions = [];

        $stmt = $this->pdo->prepare('SELECT id FROM sessions WHERE id_activite = :id_activite');
        $stmt->execute(['id_activite' => $this->id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            try {
                $sessions[] = new Session($result['id']);
            } catch (PDOException) {
                // Ignorer les sessions non trouvées
            }
        }

        return $sessions;
    }

    // Setters

    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom de peux pas être vide');
        }
        $this->nom = trim($nom);
        return $this;
    }

    public function setJeu(Jeu|int $jeu): self
    {
        if ($jeu instanceof Jeu) {
            $this->jeu = $jeu;
            $this->idJeu = $jeu->getId();
        } else {
            $this->idJeu = $jeu;
            $this->jeu = null; // Lazy loading
        }
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

    public function setTypeActivite(TypeActivite $typeActivite): self
    {
        $this->typeActivite = $typeActivite;
        return $this;
    }    public function setTypeCampagne(?TypeCampagne $typeCampagne): self
    {
        if ($typeCampagne !== null && $this->typeActivite !== TypeActivite::Campagne) {
            throw new InvalidArgumentException('Le type de campagne ne peut être défini que pour une activite de type CAMPAGNE.');
        }
        $this->typeCampagne = $typeCampagne;
        return $this;
    }

    public function setEtat(EtatActivite $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function setDescriptionCourte(?string $descriptionCourte): self
    {
        if ($descriptionCourte !== null && strlen($descriptionCourte) > 255) {
            throw new InvalidArgumentException('La description courte ne peut pas dépasser 255 caractères.');
        }
        $this->descriptionCourte = $descriptionCourte;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setNombreMaxJoueurs(int $nombreMaxJoueurs): self
    {
        if ($nombreMaxJoueurs < 0) {
            throw new InvalidArgumentException('Le nombre maximum de joueurs ne peut pas être négatif.');
        }
        if ($this->verrouille) {
            throw new InvalidArgumentException('Impossible de modifier le nombre maximum de joueurs : la activite est verrouillée.');
        }
        if ($nombreMaxJoueurs > 0) {
            $currentPlayers = $this->getNombreJoueursInscrits();
            if ($currentPlayers > $nombreMaxJoueurs) {
                throw new InvalidArgumentException(
                    'Le nombre maximum de joueurs ne peut pas être inférieur au nombre de joueurs inscrits (' . $currentPlayers . ').'
                );
            }
        }
        $this->nombreMaxJoueurs = $nombreMaxJoueurs;
        return $this;
    }

    public function setMaxJoueursSession(int $maxJoueursSession): self
    {
        if ($maxJoueursSession < 0) {
            throw new InvalidArgumentException('Le nombre maximum de joueurs ne peut pas être négatif.');
        }
        
        if ($maxJoueursSession > 0) {
            if ($this->nombreMaxJoueurs < $maxJoueursSession) {
                if ($this->getTypeCampagne() == TypeCampagne::Fermee) {
                    throw new InvalidArgumentException(
                        'Le nombre maximum de joueurs par session ne peut pas être supérieur au nombre de joueurs maximum.'
                    );
                } else {
                    $this->setNombreMaxJoueurs($maxJoueursSession);
                }
            }
        }
        
        $this->maxJoueursSession = $maxJoueursSession;
        return $this;
    }

    public function setImage(Image|string|array|null $image): self
    {
        if (!is_null($this->image)) {
            $this->image->delete();
        }
        if ($image instanceof Image) {
            $this->image = $image;
        } else {
            $this->image = Image::load($image, 
                        $this->nom . '_' . (
                                $this->getMaitreJeu()->getPseudonyme() ? $this->getMaitreJeu()->getPseudonyme() : $this->getMaitreJeu()->getNom() . $this->getMaitreJeu()->getPrenom()
                            ) . '_' . $this->getTypeActivite()->value . '_' . $this->getJeu()->getNom(), 
                        '/Activites', 
                        'Image de présentation pour la activite ' . $this->nom . ' de ' . $this->getJeu()->getNom() . ' par ' . ($this->getMaitreJeu()->getPseudonyme() ? $this->getMaitreJeu()->getPseudonyme() : $this->getMaitreJeu()->getNom()), true);
        }
        return $this;
    }

    public function setTexteAltImage(?string $texteAltImage): self
    {
        if ($texteAltImage !== null && strlen($texteAltImage) > 255) {
            throw new InvalidArgumentException('Le texte alternatif de l\'image ne peut pas dépasser 255 caractères.');
        }
        $this->texteAltImage = $texteAltImage;
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

    // Additional Methods

    /**
     * Vérifie si un utilisateur est membre de la activite.
     *
     * @param Utilisateur|string $utilisateurOuId Objet Utilisateur ou ID de l'utilisateur
     * @return bool
     */
    public function estMembre(Utilisateur|string $utilisateurOuId): bool
    {
        $idUtilisateur = $utilisateurOuId instanceof Utilisateur ? $utilisateurOuId->getId() : $utilisateurOuId;

        if (!isValidUuid($idUtilisateur)) {
            throw new InvalidArgumentException('L\'ID de l\'utilisateur doit être un UUID valide.');
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM membres_activite WHERE id_activite = :id_activite AND id_utilisateur = :id_utilisateur');
        $stmt->execute([
            'id_activite' => $this->id,
            'id_utilisateur' => $idUtilisateur,
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie s'il reste de la place dans la activite.
     *
     * @return bool
     */
    public function restePlace(): bool
    {
        if ($this->nombreMaxJoueurs === 0) {
            return true; // Pas de limite
        }
        return $this->getNombreJoueursInscrits() < $this->nombreMaxJoueurs;
    }

    /**
     * Compte le nombre de joueurs inscrits.
     *
     * @return int
     */
    public function getNombreJoueursInscrits(): int
    {
        if (isset( $this->id ) ) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM membres_activite WHERE id_activite = :id_activite');
            $stmt->execute(['id_activite' => $this->id]);
            return (int) $stmt->fetchColumn();
        }

        return -1;
    }

    /**
     * Crée une nouvelle session pour cette activite.
     *
     * @param Lieu|int $lieuOuId Objet Lieu ou ID du lieu
     * @param string $dateSession Date de la session (format Y-m-d)
     * @param string $heureDebut Heure de début (format H:i:s)
     * @param string $heureFin Heure de fin (format H:i:s)
     * @param Utilisateur|string $maitreJeuOuId Objet Utilisateur ou ID du maître du jeu
     * @return Session
     * @throws InvalidArgumentException Si les paramètres sont invalides
     * @throws PDOException En cas d'erreur SQL
     */
    public function creerSession(
        Lieu|int $lieuOuId,
        string $dateSession,
        string $heureDebut,
        string $heureFin,
        Utilisateur|string $maitreJeuOuId
    ): Session {
        $session = new Session(
            null,
            $this,
            $lieuOuId,
            $dateSession,
            $heureDebut,
            $heureFin,
            $maitreJeuOuId
        );
        $session->save();
        return $session;
    }

    /**
     * Invalide le cache des recherches.
     */
    private function invalidateCache(): void
    {
        if (!self::$cacheEnabled || !extension_loaded('apcu')) {
            return;
        }
        $cacheInfo = apcu_cache_info();
        if (!isset($cacheInfo['cache_list'])) {
            return;
        }

        // Invalider tous les caches de recherche des activites
        foreach ($cacheInfo['cache_list'] as $entry) {
            if (!isset($entry['info'])) continue;
            $key = $entry['info'];
            if (str_starts_with($key, self::$cachePrefix)) {
                apcu_delete($key);
            }
        }
    }

    public function isLocked(): bool{
        if( $this->getVerrouille() || $this->getNombreJoueursInscrits() >= $this->getNombreMaxJoueurs()){
            return true;
        }
        return false;
    }

    public function jsonSerialize(): array
    {        return [
            'id' => $this->getId(),
            'nom' => $this->nom,
            'etat' => $this->getEtat()->value,
            'id_jeu' => $this->idJeu,
            'id_maitre_jeu' => $this->idMaitreJeu,
            'jeu' => $this->getJeu()->jsonSerialize(),
            'maitre_jeu' => $this->getMaitreJeu()->jsonSerialize(),
            'type_activite' => $this->getTypeActivite()->value,
            'type_campagne' => $this->getTypeCampagne()?->value,
            'description_courte' => $this->getDescriptionCourte(),
            'description' => $this->getDescription(),
            'nombre_max_joueurs' => $this->getNombreMaxJoueurs(),
            'max_joueurs_session' => $this->getMaxJoueursSession(),
            'verrouille' => $this->getVerrouille(),
            'image' => $this->getImage() ? $this->getImage()->jsonSerialize() : null,
            'texte_alt_image' => $this->getTexteAltImage(),
            'date_creation' => $this->getDateCreation(),
        ];
    }
}