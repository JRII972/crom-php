<?php
declare(strict_types=1);

namespace App\Api;

use App\Database\Types\Session;
use App\Database\Types\JoueursSession;
use PDOException;
use InvalidArgumentException;

class SessionsApi extends APIHandler
{
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $segments = array_values(array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))));

        // Gestion des endpoints joueurs
        if (isset($segments[3]) && $segments[3] === 'joueurs') {
            if ($method === 'GET' && count($segments) === 4) {
                $this->requirePermission('SessionsApi', 'read');
                return $this->handleGetPlayers($id);
            }
            if ($method === 'POST' && count($segments) === 4) {
                $this->requirePermission('SessionsApi', 'write');
                return $this->handleAddPlayer($id);
            }
            if ($method === 'DELETE' && isset($segments[4])) {
                $this->requirePermission('SessionsApi', 'delete');
                return $this->handleRemovePlayer($id, $segments[4]);
            }
            return $this->sendResponse(405, 'error', null, 'Méthode non autorisée pour les joueurs');
        }

        // Gestion des endpoints principaux
        switch ($method) {
            case 'OPTIONS':
                return $this->handleOptions($id);
            case 'GET':
                return $this->handleGet($id);
            case 'POST':
                $this->requirePermission('SessionsApi', 'write');
                return $this->handlePost();
            case 'PUT':
                $this->requirePermission('SessionsApi', 'write');
                return $this->handlePut($id);
            case 'DELETE':
                $this->requirePermission('SessionsApi', 'delete');
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    private function handleGet(?string $id): array
    {
        if ($id !== null && is_numeric($id)) {
            try {
                $session = new Session(id: (int)$id);
                return $this->sendResponse(200, 'success', $session->jsonSerialize());
            } catch (PDOException $e) {
                return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
            }
        }

        try {
            $queryParams = $_GET;
            $partieId = isset($queryParams['partie_id']) ? (int)$queryParams['partie_id'] : 0;
            $lieuId = isset($queryParams['lieu_id']) ? (int)$queryParams['lieu_id'] : 0;
            $dateDebut = $queryParams['date_debut'] ?? '';
            $dateFin = $queryParams['date_fin'] ?? '';
            $maxJoueurs = isset($queryParams['max_joueurs']) ? (int)$queryParams['max_joueurs'] : null;
            
            // Récupération du paramètre 'categories' sous forme de tableau
            $categories = null;
            if (isset($queryParams['categories'])) {
                // Si c'est une chaîne JSON, on la décode
                if (is_string($queryParams['categories']) && $this->isJson($queryParams['categories'])) {
                    $categories = json_decode($queryParams['categories'], true);
                } 
                // Si c'est une chaîne CSV, on la divise
                else if (is_string($queryParams['categories']) && str_contains($queryParams['categories'], ',')) {
                    $categories = array_map('trim', explode(',', $queryParams['categories']));
                }
                // Si c'est une valeur unique
                else if (is_string($queryParams['categories'])) {
                    $categories = [$queryParams['categories']];
                }
            }
            
            // Récupération du paramètre 'jours' sous forme de tableau
            $jours = null;
            if (isset($queryParams['jours'])) {
                // Si c'est une chaîne JSON, on la décode
                if (is_string($queryParams['jours']) && $this->isJson($queryParams['jours'])) {
                    $jours = json_decode($queryParams['jours'], true);
                } 
                // Si c'est une chaîne CSV, on la divise
                else if (is_string($queryParams['jours']) && str_contains($queryParams['jours'], ',')) {
                    $jours = array_map('trim', explode(',', $queryParams['jours']));
                }
                // Si c'est une valeur unique
                else if (is_string($queryParams['jours'])) {
                    $jours = [$queryParams['jours']];
                }
            }

            $sessions = Session::search(
                $this->pdo, 
                $partieId, 
                $lieuId, 
                $dateDebut, 
                $dateFin, 
                $maxJoueurs, 
                $categories,
                $jours
            );

            return $this->sendResponse(200, 'success', $sessions);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    private function handlePost(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_partie'], $data['id_lieu'], $data['date_session'], $data['heure_debut'], $data['heure_fin'], $data['id_maitre_jeu'])) {
            return $this->sendResponse(400, 'error', null, 'id_partie, id_lieu, date_session, heure_debut, heure_fin, id_maitre_jeu requis');
        }

        try {
            // Vérifier que l'utilisateur est le maître du jeu ou admin
            if ($this->user['sub'] !== $data['id_maitre_jeu'] && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
                return $this->sendResponse(403, 'error', null, 'Seul le maître du jeu ou un admin peut créer une session');
            }

            $session = new Session(
                partieOuId: (int)$data['id_partie'],
                lieuOuId: (int)$data['id_lieu'],
                dateSession: $data['date_session'],
                heureDebut: $data['heure_debut'],
                heureFin: $data['heure_fin'],
                maitreJeuOuId: $data['id_maitre_jeu'],
                maxJoueurs: isset($data['nombre_max_joueurs']) ? (int)$data['nombre_max_joueurs'] : null,
                maxJoueursSession: isset($data['max_joueurs_session']) ? (int)$data['max_joueurs_session'] : null // Ajout ici
            );
            $session->save();

            return $this->sendResponse(201, 'success', $session->jsonSerialize(), 'Session créée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handlePut(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            return $this->sendResponse(400, 'error', null, 'Données requises');
        }

        try {
            $session = new Session(id: (int)$id);
            $this->verifyMasterOrAdmin($session);

            if (isset($data['id_partie'])) {
                $session->setPartie((int)$data['id_partie']);
            }
            if (isset($data['id_lieu'])) {
                $session->setLieu((int)$data['id_lieu']);
            }
            if (isset($data['date_session'])) {
                $session->setDateSession($data['date_session']);
            }
            if (isset($data['heure_debut'])) {
                $session->setHeureDebut($data['heure_debut']);
            }
            if (isset($data['heure_fin'])) {
                $session->setHeureFin($data['heure_fin']);
            }
            if (isset($data['id_maitre_jeu'])) {
                $session->setMaitreJeu($data['id_maitre_jeu']);
            }
            if (isset($data['nombre_max_joueurs'])) {
                $session->setMaxJoueurs((int)$data['nombre_max_joueurs']);
            }
            if (isset($data['max_joueurs_session'])) { // Ajout ici
                $session->setMaxJoueursSession((int)$data['max_joueurs_session']);
            }

            $session->save();
            return $this->sendResponse(200, 'success', $session->jsonSerialize(), 'Session mise à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleDelete(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        try {
            $session = new Session(id: (int)$id);
            $this->verifyMasterOrAdmin($session);

            $session->delete();
            return $this->sendResponse(204, 'success', null, 'Session supprimée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleGetPlayers(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        try {
            $session = new Session(id: (int)$id);
            $joueurs = $session->getJoueursSession();
            $serializedJoueurs = array_map(fn($joueur) => $joueur->jsonSerialize(), $joueurs);

            return $this->sendResponse(200, 'success', $serializedJoueurs);
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
        }
    }

    private function handleAddPlayer(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_utilisateur'])) {
            return $this->sendResponse(400, 'error', null, 'id_utilisateur requis');
        }

        try {
            $session = new Session(id: (int)$id);

            // Vérifier que l'utilisateur est inscrit et que c'est lui ou un admin
            if ($this->user['sub'] !== $data['id_utilisateur'] && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
                return $this->sendResponse(403, 'error', null, 'Seul l’utilisateur ou un admin peut s’inscrire');
            }

            $inscription = $session->ajouterJoueur($data['id_utilisateur']);
            return $this->sendResponse(201, 'success', $inscription->jsonSerialize(), 'Joueur inscrit avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(400, 'error', null, 'Erreur lors de l’inscription: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleRemovePlayer(?string $id, ?string $userId): array
    {
        if ($id === null || !is_numeric($id) || $userId === null) {
            return $this->sendResponse(400, 'error', null, 'ID de la session et ID de l’utilisateur requis');
        }

        try {
            $session = new Session(id: (int)$id);

            // Vérifier que l'utilisateur se désinscrit lui-même ou est admin
            if ($this->user['sub'] !== $userId && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
                return $this->sendResponse(403, 'error', null, 'Seul l’utilisateur ou un admin peut se désinscrire');
            }

            $session->retirerJoueur($userId);
            return $this->sendResponse(204, 'success', null, 'Joueur désinscrit avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session ou inscription non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes OPTIONS pour récupérer les options de filtres disponibles.
     * 
     * @param string|null $id Identifiant de la session (ignoré pour cette méthode)
     * @return array Réponse avec les options de filtres
     */
    private function handleOptions(?string $id): array
    {
        // Si un ID est fourni, on ne traite pas cette requête OPTIONS
        if ($id !== null) {
            return $this->sendResponse(405, 'error', null, 'La méthode OPTIONS n\'est pas disponible pour une session spécifique');
        }

        try {
            // Récupération des options de filtres
            $filterOptions = Session::getFilterOptions($this->pdo);
            
            // Création de la structure de réponse avec les options de filtres
            $response = [
                'filters' => [
                    'lieux' => $filterOptions['lieux'],
                    'date_debut' => $filterOptions['date_debut'],
                    'date_fin' => $filterOptions['date_fin'],
                    'max_joueurs' => $filterOptions['max_joueurs'],
                    'jours' => $filterOptions['jours'],
                    'categories' => $filterOptions['categories']
                ],
                'description' => [
                    'lieux' => 'Liste des lieux disponibles pour les sessions',
                    'date_debut' => 'Date de la plus ancienne session',
                    'date_fin' => 'Date de la session la plus éloignée',
                    'max_joueurs' => 'Nombre maximum de joueurs possible',
                    'jours' => 'Liste des jours de la semaine (1=Dimanche, 2=Lundi, ..., 7=Samedi)',
                    'categories' => 'Liste des genres/catégories de jeux'
                ]
            ];
            
            return $this->sendResponse(200, 'success', $response, 'Options de filtres pour les sessions');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la récupération des options: ' . $e->getMessage());
        }
    }

    private function verifyMasterOrAdmin(Session $session): void
    {
        if ($this->user['sub'] !== $session->getMaitreJeu()->getId() && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            throw new InvalidArgumentException('Seul le maître du jeu ou un admin peut effectuer cette action', 403);
        }
    }

    /**
     * Vérifie si une chaîne est au format JSON valide.
     *
     * @param string $string La chaîne à vérifier
     * @return bool True si la chaîne est au format JSON, false sinon
     */
    private function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}