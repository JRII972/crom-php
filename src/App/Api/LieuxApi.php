<?php

declare(strict_types=1);

namespace App\Api;

use App\Database\Database;
use App\Database\Types\Lieu;
use App\Database\Types\HorairesLieu;
use App\Database\Types\TypeRecurrence;
use PDO;
use PDOException;
use InvalidArgumentException;

require_once __DIR__ . '/../Database/Types/HorairesLieu.php';

class LieuxApi extends APIHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getConnection();
        
    }

    /**
     * Gère les requêtes HTTP pour la ressource Lieu.
     *
     * @return array Réponse JSON
     */
    public function handle(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $path = parse_url($uri, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', $path)));

        // Base route: /api/lieux
        if (count($segments) === 2 && $segments[0] === 'api' && $segments[1] === 'lieux') {
            if ($method === 'GET') {
                return $this->handleGetList();
            }
            if ($method === 'POST') {
                $this->requirePermission('LieuApi', 'write');
                return $this->handlePost();
            }
            return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }

        // Route: /api/lieux/:id
        if (count($segments) === 3 && $segments[0] === 'api' && $segments[1] === 'lieux' && is_numeric($segments[2])) {
            $id = (int)$segments[2];
            if ($method === 'GET') {
                return $this->handleGet($id);
            }
            if ($method === 'PUT') {
                $this->requirePermission('LieuApi', 'write');
                return $this->handlePut($id);
            }
            if ($method === 'DELETE') {
                $this->requirePermission('LieuApi', 'delete');
                return $this->handleDelete($id);
            }
            return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }

        // Route: /api/lieux/:id/horaires
        if (count($segments) === 4 && $segments[0] === 'api' && $segments[1] === 'lieux' && is_numeric($segments[2]) && $segments[3] === 'horaires') {
            $id = (int)$segments[2];
            if ($method === 'GET') {
                return $this->handleGetHoraires($id);
            }
            if ($method === 'POST') {
                $this->requirePermission('LieuApi', 'write');
                return $this->handlePostHoraires($id);
            }
            if ($method === 'PATCH') {
                $this->requirePermission('LieuApi', 'write');
                return $this->handlePatchHoraires($id);
            }
            if ($method === 'DELETE') {
                $this->requirePermission('LieuApi', 'delete');
                return $this->handleDeleteHoraires($id);
            }
            return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }

        return $this->sendResponse(404, 'error', null, 'Ressource non trouvée');
    }

    /**
     * Gère les requêtes GET pour la liste des lieux avec filtre optionnel par coordonnées.
     *
     * @return array Réponse JSON
     */
    private function handleGetList(): array
    {
        try {
            $queryParams = $_GET;
            $latitude = isset($queryParams['latitude']) ? (float)$queryParams['latitude'] : null;
            $longitude = isset($queryParams['longitude']) ? (float)$queryParams['longitude'] : null;
            $rayon = isset($queryParams['rayon']) ? (float)$queryParams['rayon'] : null;
            $keyword = isset($queryParams['keyword']) ? trim($queryParams['keyword']) : '';

            if ($latitude !== null && $longitude !== null && $rayon !== null) {
                // Valider les coordonnées et le rayon
                // TODO: Déplacer ces vérification dans la classe Database\Types\Lieu
                if ($latitude < -90 || $latitude > 90) {
                    return $this->sendResponse(400, 'error', null, 'La latitude doit être comprise entre -90 et 90');
                }
                if ($longitude < -180 || $longitude > 180) {
                    return $this->sendResponse(400, 'error', null, 'La longitude doit être comprise entre -180 et 180');
                }
                if ($rayon <= 0) {
                    return $this->sendResponse(400, 'error', null, 'Le rayon doit être positif');
                }

                // Recherche avec filtre géographique
                $sql = '
                    SELECT id, nom, adresse, latitude, longitude, description
                    FROM lieux
                    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
                    AND (
                        6371 * acos(
                            cos(radians(:latitude)) * cos(radians(latitude)) * 
                            cos(radians(longitude) - radians(:longitude)) + 
                            sin(radians(:latitude)) * sin(radians(latitude))
                        )
                    ) <= :rayon
                ';
                $params = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'rayon' => $rayon,
                ];

                if ($keyword !== '') {
                    $sql .= ' AND (nom LIKE :keyword OR adresse LIKE :keyword)';
                    $params['keyword'] = '%' . $keyword . '%';
                }

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $lieux = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Recherche sans filtre géographique
                $lieux = Lieu::search($this->pdo, $keyword);
            }

            return $this->sendResponse(200, 'success', $lieux);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes GET pour un lieu spécifique.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handleGet(int $id): array
    {
        try {
            $lieu = new Lieu(id: $id);
            return $this->sendResponse(200, 'success', $lieu->jsonSerialize());
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Lieu non trouvé: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes POST pour créer un nouveau lieu.
     *
     * @return array Réponse JSON
     */
    private function handlePost(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du lieu est requis et ne peut pas être vide');
        }

        try {
            $nom = trim($data['nom']);
            $adresse = isset($data['adresse']) ? trim($data['adresse']) : null;
            $latitude = isset($data['latitude']) ? (float)$data['latitude'] : null;
            $longitude = isset($data['longitude']) ? (float)$data['longitude'] : null;
            $description = isset($data['description']) ? trim($data['description']) : null;

            $lieu = new Lieu(
                nom: $nom,
                adresse: $adresse,
                latitude: $latitude,
                longitude: $longitude,
                description: $description
            );
            $lieu->save();

            return $this->sendResponse(201, 'success', $lieu->jsonSerialize(), 'Lieu créé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création du lieu: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PUT pour mettre à jour un lieu.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handlePut(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du lieu est requis et ne peut pas être vide');
        }

        try {
            $lieu = new Lieu(id: $id);
            $lieu->setNom(trim($data['nom']))
                 ->setAdresse(isset($data['adresse']) ? trim($data['adresse']) : null)
                 ->setLatitude(isset($data['latitude']) ? (float)$data['latitude'] : null)
                 ->setLongitude(isset($data['longitude']) ? (float)$data['longitude'] : null)
                 ->setDescription(isset($data['description']) ? trim($data['description']) : null);
            $lieu->save();

            return $this->sendResponse(200, 'success', $lieu->jsonSerialize(), 'Lieu mis à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Lieu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE pour supprimer un lieu.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handleDelete(int $id): array
    {
        try {
            $lieu = new Lieu(id: $id);
            $lieu->delete();
            return $this->sendResponse(204, 'success', null, 'Lieu supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Lieu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes GET pour récupérer les horaires d'un lieu.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handleGetHoraires(int $id): array
    {
        try {
            $horaires = HorairesLieu::search($this->pdo, idLieu: $id);
            return $this->sendResponse(200, 'success', $horaires);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la récupération des horaires: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes POST pour ajouter un horaire à un lieu.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handlePostHoraires(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['heure_debut'], $data['heure_fin'], $data['type_recurrence'])) {
            return $this->sendResponse(400, 'error', null, 'Heure de début, heure de fin et type de récurrence requis');
        }

        try {
            // Vérifier si le lieu existe
            new Lieu(id: $id);

            $typeRecurrence = TypeRecurrence::tryFrom($data['type_recurrence']);
            if ($typeRecurrence === null) {
                return $this->sendResponse(400, 'error', null, 'Type de récurrence invalide');
            }

            $horaire = new HorairesLieu(
                lieuOuId: $id,
                heureDebut: $data['heure_debut'],
                heureFin: $data['heure_fin'],
                typeRecurrence: $typeRecurrence,
                regleRecurrence: $data['regle_recurrence'] ?? null,
                exceptions: $data['exceptions'] ?? null,
                evenementOuId: null
            );
            $horaire->save();

            return $this->sendResponse(201, 'success', $horaire->jsonSerialize(), 'Horaire ajouté avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de l\'ajout de l\'horaire: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PATCH pour modifier un horaire d'un lieu.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handlePatchHoraires(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_horaire'])) {
            return $this->sendResponse(400, 'error', null, 'ID de l\'horaire requis pour la mise à jour');
        }

        try {
            // Vérifier si le lieu existe
            new Lieu(id: $id);

            $horaire = new HorairesLieu(id: (int)$data['id_horaire']);
            if ($horaire->getLieu()->getId() !== $id) {
                return $this->sendResponse(403, 'error', null, 'L\'horaire n\'appartient pas à ce lieu');
            }

            if (isset($data['heure_debut'])) {
                $horaire->setHeureDebut($data['heure_debut']);
            }
            if (isset($data['heure_fin'])) {
                $horaire->setHeureFin($data['heure_fin']);
            }
            if (isset($data['type_recurrence'])) {
                $typeRecurrence = TypeRecurrence::tryFrom($data['type_recurrence']);
                if ($typeRecurrence === null) {
                    return $this->sendResponse(400, 'error', null, 'Type de récurrence invalide');
                }
                $horaire->setTypeRecurrence($typeRecurrence);
            }
            if (isset($data['regle_recurrence'])) {
                $horaire->setRegleRecurrence($data['regle_recurrence']);
            }
            if (isset($data['exceptions'])) {
                $horaire->setExceptions($data['exceptions']);
            }

            $horaire->save();
            return $this->sendResponse(200, 'success', $horaire->jsonSerialize(), 'Horaire mis à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Horaire ou lieu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE pour supprimer un horaire d'un lieu.
     *
     * @param int $id Identifiant du lieu
     * @return array Réponse JSON
     */
    private function handleDeleteHoraires(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_horaire'])) {
            return $this->sendResponse(400, 'error', null, 'ID de l\'horaire requis pour la mise à jour');
        }

        try {
            // Vérifier si le lieu existe
            new Lieu(id: $id);

            $horaire = new HorairesLieu(id: (int)$data['id_horaire']);
            if ($horaire->getLieu()->getId() !== $id) {
                return $this->sendResponse(403, 'error', null, 'L\'horaire n\'appartient pas à ce lieu');
            }
            
            $horaire->delete();
            return $this->sendResponse(204, 'success', null, 'Horaire supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Horaire ou lieu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }
}