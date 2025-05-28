<?php

declare(strict_types=1);

namespace App\Api;

use App\Database\Database;
use App\Database\Types\Genre;
use PDO;
use PDOException;
use InvalidArgumentException;

class GenresApi extends APIHandler
{
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Gère les requêtes HTTP pour la ressource Genre.
     *
     * @param string|null $id Identifiant du genre (optionnel)
     * @return array Réponse JSON
     * @throws Exception
     */
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                return $this->handleGet($id);
            case 'POST':
                return $this->handlePost();
            case 'PUT':
                return $this->handlePut($id);
            case 'PATCH':
                return $this->handlePut($id); // PATCH is identical to PUT as only one field exists
            case 'DELETE':
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    /**
     * Gère les requêtes GET (récupérer un genre ou la liste des genres).
     *
     * @param string|null $id Identifiant du genre
     * @return array Réponse JSON
     */
    private function handleGet(?string $id): array
    {
        if ($id !== null && is_numeric($id)) {
            try {
                $genre = new Genre(id: (int)$id);
                return $this->sendResponse(200, 'success', $genre->jsonSerialize());
            } catch (PDOException $e) {
                return $this->sendResponse(404, 'error', null, 'Genre non trouvé: ' . $e->getMessage());
            }
        }

        // Recherche de tous les genres ou par mot-clé
        try {
            $queryParams = $_GET;
            $keyword = $queryParams['q'] ?? '';
            $genres = Genre::search($this->pdo, $keyword);
            return $this->sendResponse(200, 'success', $genres);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes POST (créer un nouveau genre).
     *
     * @return array Réponse JSON
     */
    private function handlePost(): array
    {
        $this->requirePermission('GenresApi', 'write');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du genre est requis et ne peut pas être vide');
        }

        try {
            $nom = trim($data['nom']);
            if (strlen($nom) > 100) {
                return $this->sendResponse(400, 'error', null, 'Le nom du genre ne peut pas dépasser 100 caractères');
            }

            $genre = new Genre(nom: $nom);
            $genre->save();
            return $this->sendResponse(201, 'success', $genre->jsonSerialize(), 'Genre créé avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Un genre avec ce nom existe déjà');
            }
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création du genre: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PUT et PATCH (mettre à jour un genre).
     *
     * @param string|null $id Identifiant du genre
     * @return array Réponse JSON
     */
    private function handlePut(?string $id): array
    {
        $this->requirePermission('GenresApi', 'write');

        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID du genre requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du genre est requis et ne peut pas être vide');
        }

        try {
            $nom = trim($data['nom']);
            if (strlen($nom) > 100) {
                return $this->sendResponse(400, 'error', null, 'Le nom du genre ne peut pas dépasser 100 caractères');
            }

            $genre = new Genre(id: (int)$id);
            $genre->setNom($nom);
            $genre->save();
            return $this->sendResponse(200, 'success', $genre->jsonSerialize(), 'Genre mis à jour avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Un genre avec ce nom existe déjà');
            }
            return $this->sendResponse(404, 'error', null, 'Genre non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE (supprimer un genre).
     *
     * @param string|null $id Identifiant du genre
     * @return array Réponse JSON
     */
    private function handleDelete(?string $id): array
    {
        $this->requirePermission('GenresApi', 'delete');

        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID du genre requis');
        }

        try {
            $genre = new Genre(id: (int)$id);
            $genre->delete();
            return $this->sendResponse(204, 'success', null, 'Genre supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Genre non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }
}