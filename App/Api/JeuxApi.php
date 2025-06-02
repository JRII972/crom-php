<?php

declare(strict_types=1);

namespace App\Api;

use App\Database\Database;
use App\Database\Types\Jeu;
use App\Database\Types\Genre;
use App\Database\Types\TypeJeu;
use PDO;
use PDOException;
use InvalidArgumentException;

require_once __DIR__ . '/../Database/Types/Jeu.php';

class JeuxApi extends APIHandler
{
    

    /**
     * Gère les requêtes HTTP pour la ressource Jeu.
     *
     * @param string|null $id Identifiant du jeu (optionnel)
     * @return array Réponse JSON
     * @throws ApiException
     */
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                return $this->handleGet($id);
            case 'POST':
                $this->requirePermission('JeuApi', 'write');
                return $this->handlePost();
            case 'PUT':
                $this->requirePermission('JeuApi', 'write');
                return $this->handlePut($id);
            case 'PATCH':
                $this->requirePermission('JeuApi', 'write');
                return $this->handlePatch($id);
            case 'DELETE':
                $this->requirePermission('JeuApi', 'delete');
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    /**
     * Gère les requêtes GET (récupérer un jeu ou la liste des jeux).
     *
     * @param string|null $id Identifiant du jeu
     * @return array Réponse JSON
     */
    private function handleGet(?string $id): array
    {
        if ($id !== null && is_numeric($id)) {
            try {
                $jeu = new Jeu(id: (int)$id);
                return $this->sendResponse(200, 'success', $jeu->jsonSerialize());
            } catch (PDOException $e) {
                return $this->sendResponse(404, 'error', null, 'Jeu non trouvé: ' . $e->getMessage());
            }
        }

        // Recherche de tous les jeux ou avec filtres
        try {
            $queryParams = $_GET;
            $keyword = $queryParams['keyword'] ?? '';
            $typeJeu = $queryParams['type_jeu'] ?? '';
            $genres = $queryParams['genres'] ?? '';

            $jeux = Jeu::search($this->pdo, $keyword, $typeJeu, $genres);
            return $this->sendResponse(200, 'success', $jeux);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes POST (créer un nouveau jeu).
     *
     * @return array Réponse JSON
     */
    private function handlePost(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du jeu est requis et ne peut pas être vide');
        }

        try {
            $nom = trim($data['nom']);
            if (strlen($nom) > 255) {
                return $this->sendResponse(400, 'error', null, 'Le nom du jeu ne peut pas dépasser 255 caractères');
            }

            $description = isset($data['description']) ? trim($data['description']) : null;
            if ($description !== null && strlen($description) > 65535) { // Limite pour TEXT
                return $this->sendResponse(400, 'error', null, 'La description du jeu est trop longue');
            }

            $typeJeu = isset($data['type_jeu']) ? TypeJeu::tryFrom($data['type_jeu']) : TypeJeu::Autre;
            if ($typeJeu === null) {
                return $this->sendResponse(400, 'error', null, 'Type de jeu invalide');
            }

            $jeu = new Jeu(nom: $nom, description: $description, typeJeu: $typeJeu);
            $jeu->save();

            // Associer les genres si fournis
            if (isset($data['genres']) && is_array($data['genres'])) {
                $genreIds = array_filter($data['genres'], 'is_numeric');
                foreach ($genreIds as $genreId) {
                    try {
                        $genre = new Genre(id: (int)$genreId);
                        $jeu->addGenre($genre);
                    } catch (PDOException) {
                        // Ignorer les genres non trouvés
                        continue;
                    }
                }
            }

            return $this->sendResponse(201, 'success', $jeu->jsonSerialize(), 'Jeu créé avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Un jeu avec ce nom existe déjà');
            }
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création du jeu: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PUT (mettre à jour un jeu).
     *
     * @param string|null $id Identifiant du jeu
     * @return array Réponse JSON
     */
    private function handlePut(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID du jeu requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du jeu est requis et ne peut pas être vide');
        }

        try {
            $nom = trim($data['nom']);
            if (strlen($nom) > 255) {
                return $this->sendResponse(400, 'error', null, 'Le nom du jeu ne peut pas dépasser 255 caractères');
            }

            $description = isset($data['description']) ? trim($data['description']) : null;
            if ($description !== null && strlen($description) > 65535) {
                return $this->sendResponse(400, 'error', null, 'La description du jeu est trop longue');
            }

            $typeJeu = isset($data['type_jeu']) ? TypeJeu::tryFrom($data['type_jeu']) : TypeJeu::Autre;
            if ($typeJeu === null) {
                return $this->sendResponse(400, 'error', null, 'Type de jeu invalide');
            }

            $jeu = new Jeu(id: (int)$id);
            $jeu->setNom($nom)
                ->setDescription($description)
                ->setTypeJeu($typeJeu);

            // Mettre à jour les genres si fournis
            if (isset($data['genres']) && is_array($data['genres'])) {
                $genreIds = array_filter($data['genres'], 'is_numeric');
                $currentGenres = $jeu->getGenres();

                // Supprimer les genres non présents dans la nouvelle liste
                foreach ($currentGenres as $genre) {
                    if (!in_array($genre->getId(), $genreIds)) {
                        $jeu->removeGenre($genre);
                    }
                }

                // Ajouter les nouveaux genres
                foreach ($genreIds as $genreId) {
                    try {
                        $genre = new Genre(id: (int)$genreId);
                        $jeu->addGenre($genre);
                    } catch (PDOException) {
                        // Ignorer les genres non trouvés
                        continue;
                    }
                }
            }

            $jeu->save();
            return $this->sendResponse(200, 'success', $jeu->jsonSerialize(), 'Jeu mis à jour avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Un jeu avec ce nom existe déjà');
            }
            return $this->sendResponse(404, 'error', null, 'Jeu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PATCH (mettre à jour un jeu).
     *
     * @param string|null $id Identifiant du jeu
     * @return array Réponse JSON
     */
    private function handlePatch(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID du jeu requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom']) || empty(trim($data['nom']))) {
            return $this->sendResponse(400, 'error', null, 'Le nom du jeu est requis et ne peut pas être vide');
        }

        try {
            $nom = isset($data['nom']) ? trim($data['nom']) : null;
            if (strlen($nom) > 255) {
                return $this->sendResponse(400, 'error', null, 'Le nom du jeu ne peut pas dépasser 255 caractères');
            }

            $description = isset($data['description']) ? trim($data['description']) : null;
            if ($description !== null && strlen($description) > 65535) {
                return $this->sendResponse(400, 'error', null, 'La description du jeu est trop longue');
            }

            $typeJeu = isset($data['type_jeu']) ? TypeJeu::tryFrom($data['type_jeu']) : TypeJeu::Autre;
            if ($typeJeu === null) {
                return $this->sendResponse(400, 'error', null, 'Type de jeu invalide');
            }

            $genreIds = null;
            if (isset($data['genres']) && is_array($data['genres'])) {
                $genreIds = array_filter($data['genres'], 'is_numeric');
            }
            $jeu = new Jeu(id: (int)$id);
            $jeu->update([
                'nom' => $nom,
                'description' => $description,
                'typeJeu' => $typeJeu,
                'genres' => $genreIds,
            ]);
            $jeu->save();
            return $this->sendResponse(200, 'success', $jeu->jsonSerialize(), 'Jeu mis à jour avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Un jeu avec ce nom existe déjà');
            }
            return $this->sendResponse(404, 'error', null, 'Jeu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE (supprimer un jeu).
     *
     * @param string|null $id Identifiant du jeu
     * @return array Réponse JSON
     */
    private function handleDelete(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID du jeu requis');
        }

        try {
            $jeu = new Jeu(id: (int)$id);
            $jeu->delete();
            return $this->sendResponse(204, 'success', null, 'Jeu supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Jeu non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    
}