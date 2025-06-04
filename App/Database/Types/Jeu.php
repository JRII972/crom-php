<?php

declare(strict_types=1);

namespace App\Database\Types;

use App\Utils\Image;
use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Enumération pour le type de jeu.
 */
enum TypeJeu: string
{
    case JDR = 'JDR';
    case JeuDeSociete = 'JEU_DE_SOCIETE';
    case Autre = 'AUTRE';
}

/**
 * Classe représentant un jeu dans la base de données.
 */
class Jeu extends DefaultDatabaseType
{
    private string $nom;
    private ?string $description = null;
    private TypeJeu $typeJeu = TypeJeu::Autre;
    private ?Image $image = null;
    private ?Image $icon = null;

    private static $cacheEnabled = false; // Activer/désactiver le cache
    private static $cacheTTL = 300; // 5 minutes en secondes
    private static $cachePrefix = 'jeu_search_';

    /**
     * Constructeur de la classe Jeu.
     *
     * @param int|null $id Identifiant du jeu (si fourni, charge depuis la base)
     * @param string|null $nom Nom du jeu (requis si $id est null)
     * @param string|null $description Description du jeu
     * @param Image|string|array|null $image Image de la partie
     * @param Image|string|array|null $icon Image de la partie
     * @param TypeJeu|null $typeJeu Type du jeu
     * @throws InvalidArgumentException Si les paramètres sont incohérents
     * @throws PDOException Si le jeu n'existe pas dans la base
     */
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $description = null,
        Image|string|array|null $image = null,
        Image|string|array|null $icon = null,
        ?TypeJeu $typeJeu = null
    ) {
        parent::__construct();
        $this->table = 'jeux';

        if ($id !== null && $nom === null && $description === null && $typeJeu === null) {
            // Mode : Charger le jeu depuis la base
            $this->loadFromDatabase($id);
        } elseif ($id === null && $nom !== null) {
            // Mode : Créer un nouveau jeu
            $this->setNom($nom);
            if ($description !== null) {
                $this->setDescription($description);
            }
            if ($typeJeu !== null) {
                $this->setTypeJeu($typeJeu);
            }
            if ($image !== null) {
                $this->image = Image::load($image);
            }
            if ($icon !== null) {
                $this->image = Image::load($icon);
            }
        } else {
            throw new InvalidArgumentException(
                'Vous devez fournir soit un ID seul, soit un nom (et éventuellement description et typeJeu).'
            );
        }
    }

    /**
     * Charge les données du jeu depuis la base de données.
     *
     * @param int $id Identifiant du jeu
     * @throws PDOException Si le jeu n'existe pas
     */
    private function loadFromDatabase(int $id): void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM jeux WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new PDOException('Jeu non trouvé pour l\'ID : ' . $id);
        }

        $this->id = (int) $data['id'];
        $this->nom = $data['nom'];
        $this->description = $data['description'];
        $this->typeJeu = TypeJeu::from($data['type_jeu']);
        $this->image = Image::load($data['image']);
        $this->icon = Image::load($data['icon']);
    }

    /**
     * Sauvegarde le jeu dans la base de données (insertion ou mise à jour).
     *
     * @throws PDOException En cas d'erreur SQL (ex. violation d'unicité sur nom)
     */
    public function save(): void
    {
        if (isset($this->id)) {
            // Mise à jour
            $stmt = $this->pdo->prepare('
                UPDATE jeux SET
                    nom = :nom,
                    description = :description,
                    image = :image,
                    icon = :icon,
                    type_jeu = :type_jeu
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $this->id,
                'nom' => $this->nom,
                'description' => $this->description,
                'image' => $this->image ? $this->image->getFilePath() : null,
                'icon' => $this->icon ? $this->icon->getFilePath() : null,
                'type_jeu' => $this->typeJeu->value,
            ]);
        } else {
            // Insertion
            $stmt = $this->pdo->prepare('
                INSERT INTO jeux (nom, description, type_jeu, image, icon)
                VALUES (:nom, :description, :type_jeu, :image, :icon)
            ');
            $stmt->execute([
                'nom' => $this->nom,
                'description' => $this->description,
                'image' => $this->image ? $this->image->getFilePath() : null,
                'icon' => $this->icon ? $this->icon->getFilePath() : null,
                'type_jeu' => $this->typeJeu->value,            ]);
            $this->id = (int) $this->pdo->lastInsertId();
        }
        
        // Invalider le cache
        $this->invalidateCache();
    }

    /**
     * Supprime le jeu de la base de données.
     *
     * @throws InvalidArgumentException Si l'ID n'est pas défini
     * @throws PDOException En cas d'erreur SQL
     */
    public function delete(): bool
    {        if (!isset($this->id)) {
            throw new InvalidArgumentException('Impossible de supprimer un jeu sans ID.');
        }
        $stmt = $this->pdo->prepare('DELETE FROM jeux WHERE id = :id');
        $result = $stmt->execute(['id' => $this->id]);
        
        // Invalider le cache
        $this->invalidateCache();
        
        return $result;
    }

    /**
     * Recherche des jeux avec filtres optionnels.
     *
     * @param PDO $pdo Instance PDO
     * @param string $keyword Mot-clé de recherche (optionnel)
     * @param string $typeJeu Type de jeu (optionnel)
     * @param string $genres Liste d'IDs de genres séparés par des virgules (optionnel)
     * @return array Liste des jeux avec leurs genres
     * @throws PDOException En cas d'erreur SQL
     */
    public static function search(PDO $pdo, string $keyword = '', string $typeJeu = '', string $genres = ''): array
    {
        // Générer une clé de cache unique basée sur les paramètres
        $cacheKey = self::$cachePrefix . md5(serialize([$keyword, $typeJeu, $genres]));

        // Vérifier le cache
        if (self::$cacheEnabled && extension_loaded('apcu')) {
            $cachedResult = apcu_fetch($cacheKey);
            if ($cachedResult !== false) {
                return $cachedResult;
            }
        }
        
        $sql = 'SELECT DISTINCT j.id, j.nom, j.description, j.type_jeu
                FROM jeux j
                LEFT JOIN jeux_genres jg ON j.id = jg.id_jeu
                WHERE 1=1';
        $params = [];

        if ($keyword !== '') {
            // TODO: Améliorer les performances de la recherche (ex. ajouter un index ou utiliser un moteur de recherche)
            $sql .= ' AND (j.nom LIKE :keyword OR j.description LIKE :keyword OR j.type_jeu LIKE :keyword)';
            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($typeJeu !== '' && in_array($typeJeu, ['JDR', 'JEU_DE_SOCIETE', 'AUTRE'])) {
            $sql .= ' AND j.type_jeu = :type_jeu';
            $params['type_jeu'] = $typeJeu;
        }

        if ($genres !== '') {
            $genreIds = array_filter(explode(',', $genres), 'is_numeric');
            if (!empty($genreIds)) {
                $placeholders = implode(',', array_fill(0, count($genreIds), '?'));
                $sql .= " AND jg.id_genre IN ($placeholders)";
                foreach ($genreIds as $index => $genreId) {
                    $params[$index + 1] = (int)$genreId;
                }
            }
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($jeux as $jeuData) {
            try {
                $jeu = new Jeu(id: (int)$jeuData['id']);
                $results[] = $jeu->jsonSerialize();
            } catch (\Throwable $e) {
                continue;
            }
        }
        
        // Stocker dans le cache
        if (self::$cacheEnabled && extension_loaded('apcu')) {
            apcu_store($cacheKey, $results, self::$cacheTTL);
        }

        return $results;
    }

    /**
     * Récupère la liste des genres associés au jeu.
     *
     * @return Genre[]
     * @throws PDOException En cas d'erreur SQL
     */
    public function getGenres(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT g.id, g.nom
            FROM genres g
            JOIN jeux_genres jg ON g.id = jg.id_genre
            WHERE jg.id_jeu = :id_jeu
        ');
        $stmt->execute(['id_jeu' => $this->id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $genres = [];
        foreach ($results as $data) {
            $genre = new Genre(id: (int) $data['id']);
            $genres[] = $genre;
        }

        return $genres;
    }

    /**
     * Met à jour les attributs du jeu à partir d'un tableau de données.
     *
     * @param array $data Données à mettre à jour
     * @return self
     * @throws PDOException
     */
    public function update(array $data): self
    {
        if (isset($data['nom'])) {
            $this->setNom(trim($data['nom']));
        }
        if (isset($data['description'])) {
            $this->setDescription(trim($data['description']));
        }
        if (isset($data['type_jeu'])) {
            $typeJeu = TypeJeu::tryFrom($data['type_jeu']);
            if ($typeJeu !== null) {
                $this->setTypeJeu($typeJeu);
            }
        }

        // Mettre à jour les genres si fournis
        if (isset($data['genres']) && is_array($data['genres'])) {
            $currentGenres = $this->getGenres();
            $newGenreIds = array_filter($data['genres'], 'is_numeric');

            // Supprimer les genres qui ne sont plus dans la liste
            foreach ($currentGenres as $genre) {
                if (!in_array($genre->getId(), $newGenreIds)) {
                    $this->removeGenre($genre);
                }
            }

            // Ajouter les nouveaux genres
            foreach ($newGenreIds as $genreId) {
                try {
                    $genre = new Genre(id: (int)$genreId);
                    $this->addGenre($genre);
                } catch (PDOException) {
                    // Ignorer les genres non trouvés
                    continue;
                }
            }
        }

        return $this;
    }

    /**
     * Vérifie si un genre est associé au jeu.
     *
     * @param Genre $genre Genre à vérifier
     * @return bool
     * @throws PDOException En cas d'erreur SQL
     */
    public function hasGenre(Genre $genre): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM jeux_genres
            WHERE id_jeu = :id_jeu AND id_genre = :id_genre
        ');
        $stmt->execute([
            'id_jeu' => $this->id,
            'id_genre' => $genre->getId(),
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Associe un genre au jeu.
     *
     * @param Genre $genre Genre à associer
     * @throws PDOException En cas d'erreur SQL (ex. genre déjà associé)
     */
    public function addGenre(Genre $genre): void
    {
        // Vérifier si l'association existe déjà
        if ($this->hasGenre($genre)) {
            return; // Ne rien faire si le genre est déjà associé
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO jeux_genres (id_jeu, id_genre)
            VALUES (:id_jeu, :id_genre)
        ');
        $stmt->execute([
            'id_jeu' => $this->id,
            'id_genre' => $genre->getId(),
        ]);
    }

    /**
     * Supprime l'association d'un genre avec le jeu.
     *
     * @param Genre $genre Genre à dissocier
     * @throws PDOException En cas d'erreur SQL
     */
    public function removeGenre(Genre $genre): void
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM jeux_genres
            WHERE id_jeu = :id_jeu AND id_genre = :id_genre
        ');
        $stmt->execute([
            'id_jeu' => $this->id,
            'id_genre' => $genre->getId(),
        ]);
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

        // Invalider tous les caches de recherche des jeux
        foreach ($cacheInfo['cache_list'] as $entry) {
            if (!isset($entry['info'])) continue;
            $key = $entry['info'];
            if (str_starts_with($key, self::$cachePrefix)) {
                apcu_delete($key);
            }
        }
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTypeJeu(): TypeJeu
    {
        return $this->typeJeu;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getIcon(): ?Image
    {
        return $this->icon;
    }

    // Setters

    public function setNom(string $nom): self
    {
        if (empty(trim($nom))) {
            throw new InvalidArgumentException('Le nom du jeu ne peut pas être vide.');
        }
        $this->nom = $nom;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setTypeJeu(TypeJeu $typeJeu): self
    {
        $this->typeJeu = $typeJeu;
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
                        $this->nom, 
                        '/Jeux', 
                        'Image de ' . $this->nom, true);
        }
        return $this;
    }

    public function setIcon(Image|string|array $icon): self
    {
        if (!is_null($this->icon)) {
            $this->icon->delete();
        }
        if ($icon instanceof Image) {
            $this->icon = $icon;
        } else {
            $this->icon = new Image($icon, 
                        $this->nom . '_icon', 
                        '/Jeux', 
                        'Icon de ' . $this->nom, true);
        }
        return $this;
    }

    public function jsonSerialize(): array
    {
        $genres = array_map(fn($genre) => [
            'id' => $genre->getId(),
            'nom' => $genre->getNom(),
        ], $this->getGenres());

        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'image' => $this->getImage() ? $this->getImage()->jsonSerialize() : null,
            'icon' => $this->getIcon() ? $this->getIcon()->jsonSerialize() : null,
            'description' => $this->getDescription(),
            'type_jeu' => $this->getTypeJeu()->value,
            'genres' => $genres,
        ];
    }
}