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
 * Enumération pour le type de partie.
 */
enum TypePartie: string
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
 * Classe représentant une partie dans la base de données.
 */
class Partie extends DefaultDatabaseType
{
    private ?int $idJeu;
    private ?string $nom;
    private ?Jeu $jeu = null;
    private ?string $idMaitreJeu;
    private ?Utilisateur $maitreJeu = null;
    private TypePartie $typePartie;
    private ?TypeCampagne $typeCampagne = null;
    private ?string $descriptionCourte = null;
    private ?string $description = null;
    private int $nombreMaxJoueurs = 0;
    private int $maxJoueursSession = 5;
    private bool $verrouille = false;
    private ?Image $image = null;
    private ?string $texteAltImage = null;
    private string $dateCreation;

    /**
     * Constructeur de la classe Partie.
     *
     * @param int|null $id Identifiant de la partie (si fourni, charge depuis la base)
     * @param string|null $nom Nom de la Partie
     * @param Jeu|int|null $jeuOuId Objet Jeu ou ID du jeu (requis si $id est null)
     * @param Utilisateur|string|null $maitreJeuOuId Objet Utilisateur ou ID du maître du jeu (requis si $id est null)
     * @param TypePartie|null $typePartie Type de partie (requis si $id est null)
     * @param TypeCampagne|null $typeCampagne Type de campagne
     * @param string|null $descriptionCourte Description courte
     * @param string|null $description Description complète
     * @param int|null $nombreMaxJoueurs Nombre maximum de joueurs
     * @param int|null $maxJoueursSession Nombre maximum de joueurs par session
     * @param Image|string|array|null $image Image de la partie
     * @param string|null $texteAltImage Texte alternatif de l'image
     * @param string|null $dateCreation Date de création (format Y-m-d H:i:s)
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si la partie n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        Jeu|int|null $jeuOuId = null,
        Utilisateur|string|null $maitreJeuOuId = null,
        ?TypePartie $typePartie = null,
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
        $this->table = 'parties';
        
        if ($id !== null) {
            // Mode : Charger la partie depuis la base
            $this->loadFromDatabase($id);
        } elseif ($nom !== null && $jeuOuId !== null && $maitreJeuOuId !== null && $typePartie !== null) {
            // Mode : Créer une nouvelle partie
            $this->setNom($nom);
            $this->setJeu($jeuOuId);
            $this->setMaitreJeu($maitreJeuOuId);
            $this->setTypePartie($typePartie);
            if ($typeCampagne !== null) {
                $this->setTypeCampagne($typeCampagne);
            } elseif ($typePartie == TypePartie::Campagne) {
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
                'Vous devez fournir soit un ID seul, soit jeuOuId, maitreJeuOuId, et typePartie ' .
                '(et éventuellement typeCampagne, descriptionCourte, description, nombreMaxJoueurs, urlImage, texteAltImage, dateCreation).'
            );
        }
    }

    /**
     * Charge les données de la partie depuis la base de données.
     *
     * @param int $id Identifiant de la partie
     * @throws PDOException Si la partie n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM parties WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Partie non trouvée pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->nom = $data['nom'];
        $this->idJeu = (int) $data['id_jeu'];
        $this->idMaitreJeu = $data['id_maitre_jeu'];
        $this->typePartie = TypePartie::from($data['type_partie']);
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
     * Sauvegarde la partie dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE parties SET
                    nom = :nom,
                    id_jeu = :id_jeu,
                    id_maitre_jeu = :id_maitre_jeu,
                    type_partie = :type_partie,
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
                'id_jeu' => $this->idJeu,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'type_partie' => $this->typePartie->value,
                'type_campagne' => $this->typeCampagne?->value,
                'description_courte' => $this->descriptionCourte,
                'description' => $this->description,
                'nombre_max_joueurs' => $this->nombreMaxJoueurs,
                'max_joueurs_session' => $this->maxJoueursSession,
                'verrouille' => $this->verrouille ? 1 : 0,
                'image' => $this->image ? $this->image->getFilePath() : null,
                'texte_alt_image' => $this->texteAltImage
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO parties (
                    nom, id_jeu, id_maitre_jeu, type_partie, type_campagne, description_courte,
                    description, nombre_max_joueurs, max_joueurs_session, verrouille, image, texte_alt_image
                )
                VALUES (
                    :nom, :id_jeu, :id_maitre_jeu, :type_partie, :type_campagne, :description_courte,
                    :description, :nombre_max_joueurs, :max_joueurs_session, :verrouille, :image, :texte_alt_image
                )
            ');
            $stmt->execute([
                'nom' => $this->nom,
                'id_jeu' => $this->idJeu,
                'id_maitre_jeu' => $this->idMaitreJeu,
                'type_partie' => $this->typePartie->value,
                'type_campagne' => $this->typeCampagne?->value,
                'description_courte' => $this->descriptionCourte,
                'description' => $this->description,
                'nombre_max_joueurs' => $this->nombreMaxJoueurs,
                'max_joueurs_session' => $this->maxJoueursSession,
                'verrouille' => $this->verrouille ? 1 : 0,
                'image' => $this->image ? $this->image->getFilePath() : null,
                'texte_alt_image' => $this->texteAltImage,
            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
    }

    /**
     * Supprime la partie de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer une partie sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM parties WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucune partie supprimée : partie non trouvée.');
        }

        return true;
    }

    /**
     * Recherche des parties avec filtre optionnel par jeu, maître du jeu, type de partie, ou mot-clé sur nom et description.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé pour rechercher dans nom et description (optionnel)
     * @param int $idJeu ID du jeu (optionnel)
     * @param string $idMaitreJeu ID du maître du jeu (optionnel)
     * @param string $typePartie Type de partie (optionnel, CAMPAGNE, ONESHOT, JEU_DE_SOCIETE, EVENEMENT)
     * @return array Liste des parties
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $keyword = '', int $idJeu = 0, string $idMaitreJeu = '', string $typePartie = ''): array
    {
        $sql = 'SELECT id, nom, id_jeu, id_maitre_jeu, type_partie, type_campagne, description_courte, description, nombre_max_joueurs, max_joueurs_session, verrouille, image, texte_alt_image, date_creation FROM parties WHERE 1=1';
        $params = [];

        if ($idJeu > 0) {
            $sql .= ' AND id_jeu = :id_jeu';
            $params['id_jeu'] = $idJeu;
        }
        if ($idMaitreJeu !== '') {
            $sql .= ' AND id_maitre_jeu = :id_maitre_jeu';
            $params['id_maitre_jeu'] = $idMaitreJeu;
        }
        if ($typePartie !== '' && in_array($typePartie, [
            TypePartie::Campagne->value,
            TypePartie::Oneshot->value,
            TypePartie::JeuDeSociete->value,
            TypePartie::Evenement->value
        ], true)) {
            $sql .= ' AND type_partie = :type_partie';
            $params['type_partie'] = $typePartie;
        }
        if ($keyword !== '') {
            $sql .= ' AND (nom LIKE :keyword OR description LIKE :keyword)';
            $params['keyword'] = '%' . $keyword . '%';
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

    public function getTypePartie(): TypePartie
    {
        return $this->typePartie;
    }

    public function getTypeCampagne(): ?TypeCampagne
    {
        return $this->typeCampagne;
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
     * Récupère la liste des membres de la partie.
     *
     * @return MembrePartie[]
     */
    public function getMembresPartie(): array
    {
        $membres = [];

        $stmt = $this->pdo->prepare('SELECT id_utilisateur FROM membres_partie WHERE id_partie = :id_partie');
        $stmt->execute(['id_partie' => $this->id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            try {
                $utilisateur = new Utilisateur($result['id_utilisateur']);
                $membres[] = $utilisateur; // À ajuster si MembrePartie est requis
            } catch (PDOException) {
                // Ignorer les utilisateurs non trouvés
            }
        }

        return $membres;
    }

    /**
     * Récupère la liste des sessions associées à la partie.
     *
     * @return Session[]
     */
    public function getSessions(): array
    {
        $sessions = [];

        $stmt = $this->pdo->prepare('SELECT id FROM sessions WHERE id_partie = :id_partie');
        $stmt->execute(['id_partie' => $this->id]);
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

    public function setTypePartie(TypePartie $typePartie): self
    {
        $this->typePartie = $typePartie;
        return $this;
    }

    public function setTypeCampagne(?TypeCampagne $typeCampagne): self
    {
        if ($typeCampagne !== null && $this->typePartie !== TypePartie::Campagne) {
            throw new InvalidArgumentException('Le type de campagne ne peut être défini que pour une partie de type CAMPAGNE.');
        }
        $this->typeCampagne = $typeCampagne;
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
            throw new InvalidArgumentException('Impossible de modifier le nombre maximum de joueurs : la partie est verrouillée.');
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
            $this->image = new Image($image, 
                        $this->nom . '_' . (
                                $this->getMaitreJeu()->getPseudonyme() ? $this->getMaitreJeu()->getPseudonyme() : $this->getMaitreJeu()->getNom() . $this->getMaitreJeu()->getPrenom()
                            ) . '_' . $this->getTypePartie()->value . '_' . $this->getJeu()->getNom(), 
                        '/Parties', 
                        'Image de présentation pour la partie ' . $this->nom . ' de ' . $this->getJeu()->getNom() . ' par ' . ($this->getMaitreJeu()->getPseudonyme() ? $this->getMaitreJeu()->getPseudonyme() : $this->getMaitreJeu()->getNom()), true);
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
     * Vérifie si un utilisateur est membre de la partie.
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

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM membres_partie WHERE id_partie = :id_partie AND id_utilisateur = :id_utilisateur');
        $stmt->execute([
            'id_partie' => $this->id,
            'id_utilisateur' => $idUtilisateur,
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie s'il reste de la place dans la partie.
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
    private function getNombreJoueursInscrits(): int
    {
        if (isset( $this->id ) ) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM membres_partie WHERE id_partie = :id_partie');
            $stmt->execute(['id_partie' => $this->id]);
            return (int) $stmt->fetchColumn();
        }

        return -1;
    }

    /**
     * Crée une nouvelle session pour cette partie.
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->nom,
            'id_jeu' => $this->idJeu,
            'id_maitre_jeu' => $this->idMaitreJeu,
            'jeu' => $this->getJeu()->jsonSerialize(),
            'maitre_jeu' => $this->getMaitreJeu()->jsonSerialize(),
            'type_partie' => $this->getTypePartie()->value,
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