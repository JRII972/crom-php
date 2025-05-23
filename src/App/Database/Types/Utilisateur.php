<?php

declare(strict_types=1);

namespace App\Database\Types;

use App\Database\Database;
use App\Utils\Image;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;
use RuntimeException;

require_once __DIR__ . '/../config.php';

/**
 * Enumération pour le sexe de l'utilisateur.
 */
enum Sexe: string
{
    case Masculin = 'M';
    case Feminin = 'F';
    case Autre = 'Autre';
}

/**
 * Enumération pour le type d'utilisateur.
 */
enum TypeUtilisateur: string
{
    case NonInscrit = 'NON_INSCRIT';
    case Inscrit = 'INSCRIT';
    case Administrateur = 'ADMINISTRATEUR';
}

/**
 * Classe représentant un utilisateur dans la base de données.
 */
class Utilisateur extends DefaultDatabaseType
{
    private string $prenom;
    private string $nom;
    private ?string $email;
    private string $nomUtilisateur;
    private ?DateTime $dateDeNaissance = null;
    private Sexe $sexe;
    private ?string $idDiscord = null;
    private ?string $pseudonyme = null;
    private string $motDePasse;
    private ?Image $image = null;
    private TypeUtilisateur $typeUtilisateur = TypeUtilisateur::Inscrit;
    private ?DateTime $dateInscription = null;
    private bool $ancienUtilisateur = false;
    private bool $premiereConnexion = true;
    private DateTime $dateCreation;

    /**
     * Constructeur de la classe Utilisateur.
     *
     * @param string|null $id Identifiant UUID de l'utilisateur (si fourni, charge depuis la base)
     * @param string|null $prenom Prénom de l'utilisateur
     * @param string|null $nom Nom de l'utilisateur
     * @param string|null $email Email de l'utilisateur
     * @param string|null $nomUtilisateur Nom d'utilisateur (correspond à 'login' dans la base)
     * @param DateTime|null $dateDeNaissance Date de naissance
     * @param Sexe|null $sexe Sexe de l'utilisateur
     * @param string|null $idDiscord ID Discord de l'utilisateur
     * @param string|null $pseudonyme Pseudonyme de l'utilisateur
     * @param string|null $motDePasse Hash du mot de passe
     * @param Image|string|array|null $image Image de l'utilisateur
     * @param TypeUtilisateur|null $typeUtilisateur Type d'utilisateur
     * @param DateTime|null $dateInscription Date d'inscription
     * @param bool|null $ancienUtilisateur Statut d'ancien utilisateur
     * @param bool|null $premiereConnexion Statut de première connexion
     * @param DateTime|null $dateCreation Date de création
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si l'utilisateur n'existe pas dans la base
     */
    public function __construct(
        ?string $id = null,
        ?string $prenom = null,
        ?string $nom = null,
        ?string $email = null,
        ?string $nomUtilisateur = null,
        ?DateTime $dateDeNaissance = null,
        ?Sexe $sexe = null,
        ?string $idDiscord = null,
        ?string $pseudonyme = null,
        ?string $motDePasse = null,
        Image|string|array|null $image = null,
        ?TypeUtilisateur $typeUtilisateur = null,
        ?DateTime $dateInscription = null,
        ?bool $ancienUtilisateur = null,
        ?bool $premiereConnexion = null,
        ?DateTime $dateCreation = null
    ) {
        parent::__construct();
        $this->table = 'utilisateurs';

        if ($id !== null && $prenom === null && $nom === null && $email === null && $nomUtilisateur === null && $dateDeNaissance === null && $sexe === null && $motDePasse === null && $image === null && $typeUtilisateur === null && $dateInscription === null && $ancienUtilisateur === null && $premiereConnexion === null && $dateCreation === null) {
            // Mode : Charger l'utilisateur depuis la base avec l'ID
            $this->loadFromDatabase($id);
        } elseif ($id === null && $prenom !== null && $nom !== null && $nomUtilisateur !== null && $motDePasse !== null && $sexe !== null) {
            // Mode : Créer un nouvel utilisateur
            $this->setId(generateUUID());
            $this->setPrenom($prenom);
            $this->setNom($nom);
            $this->setNomUtilisateur($nomUtilisateur);
            $this->setMotDePasse($motDePasse);
            $this->setSexe($sexe);

            if ($email !== null) {
                $this->setEmail($email);
            }
            if ($dateDeNaissance !== null) {
                $this->setDateDeNaissance($dateDeNaissance);
            }
            if ($idDiscord !== null) {
                $this->setIdDiscord($idDiscord);
            }
            if ($pseudonyme !== null) {
                $this->setPseudonyme($pseudonyme);
            }
            if ($image !== null) {
                $this->setImage($image);
            }
            if ($typeUtilisateur !== null) {
                $this->setTypeUtilisateur($typeUtilisateur);
            }
            if ($dateInscription !== null) {
                $this->setDateInscription($dateInscription);
            }
            if ($ancienUtilisateur !== null) {
                $this->setAncienUtilisateur($ancienUtilisateur);
            }
            if ($premiereConnexion !== null) {
                $this->setPremiereConnexion($premiereConnexion);
            }
            $this->dateCreation = $dateCreation ?? new DateTime();
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit les paramètres obligatoires (prenom, nom, nomUtilisateur, motDePasse, sexe) et les paramètres optionnels.'
            );
        }
    }

    /**
     * Charge les données de l'utilisateur depuis la base de données.
     *
     * @param string $id Identifiant UUID de l'utilisateur
     * @throws PDOException Si l'utilisateur n'existe pas
     * @throws InvalidArgumentException Si l'ID est invalide
     */
    private function loadFromDatabase(string $id): void
    {
        if (!isValidUuid($id)) {
            throw new InvalidArgumentException('L\'identifiant doit être un UUID valide.');
        }

        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Utilisateur non trouvé pour l\'ID : ' . $id);
        }

        $this->id = $data['id'];
        $this->prenom = $data['prenom'];
        $this->nom = $data['nom'];
        $this->email = $data['email'];
        $this->nomUtilisateur = $data['login'];
        $this->dateDeNaissance = $data['date_de_naissance'] ? new DateTime($data['date_de_naissance']) : null;
        $this->sexe = Sexe::from($data['sexe']);
        $this->idDiscord = $data['id_discord'];
        $this->pseudonyme = $data['pseudonyme'];
        $this->motDePasse = $data['mot_de_passe'];
        $this->image = Image::load($data['image']);
        $this->typeUtilisateur = TypeUtilisateur::from($data['type_utilisateur']);
        $this->dateInscription = $data['date_inscription'] ? new DateTime($data['date_inscription']) : null;
        $this->ancienUtilisateur = (bool) $data['ancien_utilisateur'];
        $this->premiereConnexion = (bool) $data['premiere_connexion'];
        $this->dateCreation = new DateTime($data['date_creation']);
    }

    /**
     * Sauvegarde l'utilisateur dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL (ex. violation d'unicité sur email ou nom_utilisateur)
     */
    public function save(): void
    {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        $exists = $stmt->fetchColumn() > 0;

        // TODO: Changer le nom de la photo de profil lors du changement de nom ou de pseudo

        if ($exists) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE utilisateurs SET
                    prenom = :prenom,
                    nom = :nom,
                    email = :email,
                    login = :nom_utilisateur,
                    date_de_naissance = :date_de_naissance,
                    sexe = :sexe,
                    id_discord = :id_discord,
                    pseudonyme = :pseudonyme,
                    mot_de_passe = :mot_de_passe,
                    image = :image,
                    type_utilisateur = :type_utilisateur,
                    date_inscription = :date_inscription,
                    ancien_utilisateur = :ancien_utilisateur,
                    premiere_connexion = :premiere_connexion,
                    date_creation = :date_creation
                WHERE id = :id
            ');
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO utilisateurs (
                    id, prenom, nom, email, login, date_de_naissance, sexe,
                    id_discord, pseudonyme, mot_de_passe, image, type_utilisateur,
                    date_inscription, ancien_utilisateur, premiere_connexion, date_creation
                ) VALUES (
                    :id, :prenom, :nom, :email, :nom_utilisateur, :date_de_naissance, :sexe,
                    :id_discord, :pseudonyme, :mot_de_passe, :image, :type_utilisateur,
                    :date_inscription, :ancien_utilisateur, :premiere_connexion, :date_creation
                )
            ');
        }

        $stmt->execute([
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'email' => $this->email,
            'nom_utilisateur' => $this->nomUtilisateur,
            'date_de_naissance' => $this->dateDeNaissance ? $this->dateDeNaissance->format('Y-m-d') : null,
            'sexe' => $this->sexe->value,
            'id_discord' => $this->idDiscord,
            'pseudonyme' => $this->pseudonyme,
            'mot_de_passe' => $this->motDePasse,
            'image' => $this->image ? $this->image->getFilePath() : null,
            'type_utilisateur' => $this->typeUtilisateur->value,
            'date_inscription' => $this->dateInscription ? $this->dateInscription->format('Y-m-d') : null,
            'ancien_utilisateur' => (int) $this->ancienUtilisateur,
            'premiere_connexion' => (int) $this->premiereConnexion,
            'date_creation' => $this->dateCreation->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Supprime l'utilisateur de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {
        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un utilisateur sans ID.');
        }
        if ($this->image !== null) {
            $this->image->delete();
        }
        $stmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        if ($stmt->rowCount() === 0) {
            throw new PDOException('Aucun utilisateur supprimé : utilisateur non trouvé.');
        }

        return true;
    }

    /**
     * Recherche des utilisateurs avec filtre optionnel par email, nom d'utilisateur ou type d'utilisateur.
     *
     * @param PDO $pdo Instance PDO
     * @param string $email Email de l'utilisateur (optionnel)
     * @param string $nomUtilisateur Nom d'utilisateur (optionnel)
     * @param string $typeUtilisateur Type d'utilisateur (optionnel, NON_INSCRIT, INSCRIT, ADMINISTRATEUR)
     * @return array Liste des utilisateurs
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $email = '', string $nomUtilisateur = '', string $typeUtilisateur = ''): array
    {
        $sql = 'SELECT id, prenom, nom, email, login, date_de_naissance, sexe, id_discord, pseudonyme, image, type_utilisateur, date_inscription, ancien_utilisateur, premiere_connexion, date_creation FROM utilisateurs WHERE 1=1';
        $params = [];

        if ($email !== '') {
            $sql .= ' AND email = :email';
            $params['email'] = $email;
        }
        if ($nomUtilisateur !== '') {
            $sql .= ' AND login = :nom_utilisateur';
            $params['nom_utilisateur'] = $nomUtilisateur;
        }
        if ($typeUtilisateur !== '' && in_array($typeUtilisateur, [
            TypeUtilisateur::NonInscrit->value,
            TypeUtilisateur::Inscrit->value,
            TypeUtilisateur::Administrateur->value
        ], true)) {
            $sql .= ' AND type_utilisateur = :type_utilisateur';
            $params['type_utilisateur'] = $typeUtilisateur;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche un utilisateur par son login.
     *
     * Cette méthode prépare et exécute une requête SQL pour trouver l'identifiant
     * d'un utilisateur correspondant au login fourni. Si un utilisateur est trouvé,
     * une nouvelle instance de la classe est retournée avec l'identifiant de l'utilisateur.
     * Sinon, la méthode retourne null.
     *
     * @param PDO    $pdo   Instance de connexion à la base de données.
     * @param string $login Le login de l'utilisateur à rechercher.
     *
     * @return self|null Retourne une instance de la classe si l'utilisateur est trouvé, sinon null.
     */
    public static function findByLogin(PDO $pdo, $login): ?self
    {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE login = :login');
        $stmt->execute(['login' => $login]);
        $userId = $stmt->fetchColumn();

        if ($userId === false) {
            return null;
        }

        return new self($userId);
    }

    /**
     * Vérifie les identifiants de connexion.
     *
     * @param string $login Nom d'utilisateur
     * @param string $password Mot de passe en clair
     * @return Utilisateur|null L'utilisateur si les identifiants sont valides, null sinon
     */
    public function checkLogin(string $login, string $password): ?static
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE login = :login AND mot_de_passe = :mot_de_passe');
            $hashedPassword = md5($password . PASSWORD_SALT);
            $stmt->execute([
                'login' => $login,
                'mot_de_passe' => $hashedPassword
            ]);
            $userId = $stmt->fetchColumn();

            if ($userId === false) {
                return null;
            }

            return new Utilisateur($userId);
        } catch (PDOException $e) {
            error_log("Login check failed: " . $e->getMessage());
            return null;
        }
    }

    // Getters

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNomUtilisateur(): string
    {
        return $this->nomUtilisateur;
    }

    public function getDateDeNaissance(): ?DateTime
    {
        return $this->dateDeNaissance;
    }

    public function getSexe(): Sexe
    {
        return $this->sexe;
    }

    public function getIdDiscord(): ?string
    {
        return $this->idDiscord;
    }

    public function getPseudonyme(): ?string
    {
        return $this->pseudonyme;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getTypeUtilisateur(): TypeUtilisateur
    {
        return $this->typeUtilisateur;
    }

    public function getDateInscription(): ?DateTime
    {
        return $this->dateInscription;
    }

    public function getAncienUtilisateur(): bool
    {
        return $this->ancienUtilisateur;
    }

    public function getPremiereConnexion(): bool
    {
        return $this->premiereConnexion;
    }

    public function getDateCreation(): DateTime
    {
        return $this->dateCreation;
    }

    /**
     * Calcule l'âge de l'utilisateur en années.
     *
     * @return int|null
     */
    public function getAge(): ?int
    {
        return $this->dateDeNaissance ? (new DateTime())->diff($this->dateDeNaissance)->y : null;
    }

    /**
     * Calcule l'ancienneté de l'utilisateur en années.
     *
     * @return int
     */
    public function getAnneesAnciennete(): int
    {
        if ($this->dateInscription === null) {
            return 0;
        }
        return (new DateTime())->diff($this->dateInscription)->y;
    }

    // Setters

    public function setId(string $id): self
    {
        if (!isValidUuid($id)) {
            throw new InvalidArgumentException('L\'identifiant doit être un UUID valide.');
        }
        $this->id = $id;
        return $this;
    }

    public function setPrenom(string $prenom): self
    {
        if (empty(trim($prenom))) {
            throw new InvalidArgumentException('Le prénom ne peut pas être vide.');
        }
        $this->prenom = $prenom;
        return $this;
    }

    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom ne peut pas être vide.');
        }
        $this->nom = $nom;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('L\'email doit être valide.');
        }
        $this->email = $email;
        return $this;
    }

    public function setNomUtilisateur(string $nomUtilisateur): self
    {
        if (empty(trim($nomUtilisateur))) {
            throw new InvalidArgumentException('Le nom d\'utilisateur ne peut pas être vide.');
        }
        $this->nomUtilisateur = $nomUtilisateur;
        return $this;
    }

    public function setDateDeNaissance(?DateTime $dateDeNaissance): self
    {
        $this->dateDeNaissance = $dateDeNaissance;
        return $this;
    }

    public function setSexe(Sexe|string $sexe): self
    {
        if (is_string($sexe)) {
            try {
                $sexe = Sexe::from($sexe);
            } catch (\Throwable $th) {
                throw new InvalidArgumentException('Sexe invalide : M, F, Autre');
            }
        }
        $this->sexe = $sexe;
        return $this;
    }

    public function setIdDiscord(?string $idDiscord): self
    {
        if (!empty(trim($idDiscord))) {
            $this->idDiscord = $idDiscord;
        }
        return $this;
    }

    public function setPseudonyme(?string $pseudonyme): self
    {
        $this->pseudonyme = $pseudonyme;
        return $this;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        if (empty(trim($motDePasse))) {
            throw new InvalidArgumentException('Le mot de passe ne peut pas être vide.');
        }
        $this->motDePasse = md5($motDePasse . PASSWORD_SALT);
        return $this;
    }

    public function setImage(Image|string|array $image): self
    {
        if (!is_null($this->image)) {
            $this->image->delete();
        }
        if ($image instanceof Image) {
            $this->image = $image;
        } else {
            $this->image = new Image($image, (
                $this->pseudonyme ? $this->pseudonyme : $this->prenom . $this->nom
            ) . '_pp', '/ProfilePicture');
        }
        return $this;
    }

    public function setTypeUtilisateur(TypeUtilisateur|string $typeUtilisateur): self
    {
        if (is_string($typeUtilisateur)) {
            try {
                $typeUtilisateur = TypeUtilisateur::from($typeUtilisateur);
            } catch (\Throwable $th) {
                throw new InvalidArgumentException("TypeUtilisateur invalide : '$typeUtilisateur'");
            }
        }
        $this->typeUtilisateur = $typeUtilisateur;
        return $this;
    }

    public function setDateInscription(?DateTime $dateInscription): self
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function setAncienUtilisateur(bool $ancienUtilisateur): self
    {
        $this->ancienUtilisateur = $ancienUtilisateur;
        return $this;
    }

    public function setPremiereConnexion(bool $premiereConnexion): self
    {
        $this->premiereConnexion = $premiereConnexion;
        return $this;
    }

    public function setDateCreation(DateTime $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'prenom' => $this->getPrenom(),
            'nom' => $this->getNom(),
            'email' => $this->getEmail(),
            'login' => $this->getNomUtilisateur(),
            'date_de_naissance' => $this->getDateDeNaissance() ? $this->getDateDeNaissance()->format('Y-m-d') : null,
            'sexe' => $this->getSexe()->value,
            'id_discord' => $this->getIdDiscord(),
            'pseudonyme' => $this->getPseudonyme(),
            'image' => $this->getImage() ? $this->getImage()->getFilePath() : null,
            'type_utilisateur' => $this->getTypeUtilisateur()->value,
            'date_inscription' => $this->getDateInscription() ? $this->getDateInscription()->format('Y-m-d') : null,
            'ancien_utilisateur' => $this->getAncienUtilisateur(),
            'premiere_connexion' => $this->getPremiereConnexion(),
            'date_creation' => $this->getDateCreation()->format('Y-m-d H:i:s'),
            'age' => $this->getAge(),
            'annees_anciennete' => $this->getAnneesAnciennete(),
        ];
    }
}