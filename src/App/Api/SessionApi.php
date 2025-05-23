<?php

declare(strict_types=1);

namespace App\Api;

use App\Database\Database;
use App\Database\Types\Session;
use PDO;
use PDOException;
use InvalidArgumentException;
use DateTime;

class SessionApi extends APIHandler
{

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getConnection();
    }

    /**
     * Gère les requêtes HTTP pour la ressource Session.
     *
     * @param string|null $id Identifiant de la session (optionnel)
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
                return $this->handlePost();
            case 'PUT':
                return $this->handlePut($id);
            case 'PATCH':
                return $this->handlePatch($id);
            case 'DELETE':
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    /**
     * Gère les requêtes GET (récupérer une session ou la liste des sessions).
     *
     * @param string|null $id Identifiant de la session
     * @return array Réponse JSON
     */
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

        // Recherche de toutes les sessions ou avec filtres
        try {
            $queryParams = $_GET;
            $keyword = $queryParams['keyword'] ?? '';
            $sessions = Session::search($this->pdo, $keyword);
            return $this->sendResponse(200, 'success', $sessions);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes POST (créer une nouvelle session).
     *
     * @return array Réponse JSON
     */
    private function handlePost(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_partie']) || !isset($data['id_lieu']) || !isset($data['date_session']) ||
            !isset($data['heure_debut']) || !isset($data['heure_fin']) || !isset($data['id_maitre_jeu'])) {
            return $this->sendResponse(400, 'error', null, 'Les champs id_partie, id_lieu, date_session, heure_debut, heure_fin et id_maitre_jeu sont requis');
        }

        try {
            // Validation des champs
            if (!is_numeric($data['id_partie'])) {
                return $this->sendResponse(400, 'error', null, 'L\'ID de la partie doit être un entier');
            }
            if (!is_numeric($data['id_lieu'])) {
                return $this->sendResponse(400, 'error', null, 'L\'ID du lieu doit être un entier');
            }
            if (!isValidDate($data['date_session'])) {
                return $this->sendResponse(400, 'error', null, 'La date de session doit être au format Y-m-d');
            }
            if (!isValidTime($data['heure_debut'])) {
                return $this->sendResponse(400, 'error', null, 'L\'heure de début doit être au format H:i:s');
            }
            if (!isValidTime($data['heure_fin'])) {
                return $this->sendResponse(400, 'error', null, 'L\'heure de fin doit être au format H:i:s');
            }
            if (!isValidUuid($data['id_maitre_jeu'])) {
                return $this->sendResponse(400, 'error', null, 'L\'ID du maître du jeu doit être un UUID valide');
            }
            if (isset($data['max_joueurs']) && (!is_numeric($data['max_joueurs']) || $data['max_joueurs'] < 0)) {
                return $this->sendResponse(400, 'error', null, 'Le nombre maximum de joueurs doit être un entier non négatif');
            }

            // Validation de l'heure de fin
            $debut = DateTime::createFromFormat('Y-m-d H:i:s', $data['date_session'] . ' ' . $data['heure_debut']);
            $fin = DateTime::createFromFormat('Y-m-d H:i:s', $data['date_session'] . ' ' . $data['heure_fin']);
            if ($fin <= $debut) {
                return $this->sendResponse(400, 'error', null, 'L\'heure de fin doit être postérieure à l\'heure de début');
            }

            // Vérification des clés étrangères
            if (!$this->existsInTable('parties', 'id', $data['id_partie'])) {
                return $this->sendResponse(400, 'error', null, 'La partie spécifiée n\'existe pas');
            }
            if (!$this->existsInTable('lieux', 'id', $data['id_lieu'])) {
                return $this->sendResponse(400, 'error', null, 'Le lieu spécifié n\'existe pas');
            }
            if (!$this->existsInTable('utilisateurs', 'id', $data['id_maitre_jeu'])) {
                return $this->sendResponse(400, 'error', null, 'L\'utilisateur (maître du jeu) spécifié n\'existe pas');
            }

            $session = new Session(
                partieOuId: (int)$data['id_partie'],
                lieuOuId: (int)$data['id_lieu'],
                dateSession: $data['date_session'],
                heureDebut: $data['heure_debut'],
                heureFin: $data['heure_fin'],
                maitreJeuOuId: $data['id_maitre_jeu'],
                maxJoueurs: isset($data['max_joueurs']) ? (int)$data['max_joueurs'] : null
            );
            $session->save();

            return $this->sendResponse(201, 'success', $session->jsonSerialize(), 'Session créée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création de la session: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PUT (mettre à jour une session).
     *
     * @param string|null $id Identifiant de la session
     * @return array Réponse JSON
     */
    private function handlePut(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_partie']) || !isset($data['id_lieu']) || !isset($data['date_session']) ||
            !isset($data['heure_debut']) || !isset($data['heure_fin']) || !isset($data['id_maitre_jeu'])) {
            return $this->sendResponse(400, 'error', null, 'Les champs id_partie, id_lieu, date_session, heure_debut, heure_fin et id_maitre_jeu sont requis');
        }

        try {
            // Validation des champs
            if (!is_numeric($data['id_partie'])) {
                return $this->sendResponse(400, 'error', null, 'L\'ID de la partie doit être un entier');
            }
            if (!is_numeric($data['id_lieu'])) {
                return $this->sendResponse(400, 'error', null, 'L\'ID du lieu doit être un entier');
            }
            if (!isValidDate($data['date_session'])) {
                return $this->sendResponse(400, 'error', null, 'La date de session doit être au format Y-m-d');
            }
            if (!isValidTime($data['heure_debut'])) {
                return $this->sendResponse(400, 'error', null, 'L\'heure de début doit être au format H:i:s');
            }
            if (!isValidTime($data['heure_fin'])) {
                return $this->sendResponse(400, 'error', null, 'L\'heure de fin doit être au format H:i:s');
            }
            if (!isValidUuid($data['id_maitre_jeu'])) {
                return $this->sendResponse(400, 'error', null, 'L\'ID du maître du jeu doit être un UUID valide');
            }
            if (isset($data['max_joueurs']) && (!is_numeric($data['max_joueurs']) || $data['max_joueurs'] < 0)) {
                return $this->sendResponse(400, 'error', null, 'Le nombre maximum de joueurs doit être un entier non négatif');
            }

            // Validation de l'heure de fin
            $debut = DateTime::createFromFormat('Y-m-d H:i:s', $data['date_session'] . ' ' . $data['heure_debut']);
            $fin = DateTime::createFromFormat('Y-m-d H:i:s', $data['date_session'] . ' ' . $data['heure_fin']);
            if ($fin <= $debut) {
                return $this->sendResponse(400, 'error', null, 'L\'heure de fin doit être postérieure à l\'heure de début');
            }

            // Vérification des clés étrangères
            if (!$this->existsInTable('parties', 'id', $data['id_partie'])) {
                return $this->sendResponse(400, 'error', null, 'La partie spécifiée n\'existe pas');
            }
            if (!$this->existsInTable('lieux', 'id', $data['id_lieu'])) {
                return $this->sendResponse(400, 'error', null, 'Le lieu spécifié n\'existe pas');
            }
            if (!$this->existsInTable('utilisateurs', 'id', $data['id_maitre_jeu'])) {
                return $this->sendResponse(400, 'error', null, 'L\'utilisateur (maître du jeu) spécifié n\'existe pas');
            }

            $session = new Session(id: (int)$id);
            $session->setPartie((int)$data['id_partie'])
                    ->setLieu((int)$data['id_lieu'])
                    ->setDateSession($data['date_session'])
                    ->setHeureDebut($data['heure_debut'])
                    ->setHeureFin($data['heure_fin'])
                    ->setMaitreJeu($data['id_maitre_jeu'])
                    ->setMaxJoueurs(isset($data['max_joueurs']) ? (int)$data['max_joueurs'] : null);
            $session->save();

            return $this->sendResponse(200, 'success', $session->jsonSerialize(), 'Session mise à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PATCH (mise à jour partielle d'une session).
     *
     * @param string|null $id Identifiant de la session
     * @return array Réponse JSON
     */
    private function handlePatch(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data)) {
            return $this->sendResponse(400, 'error', null, 'Aucun champ fourni pour la mise à jour');
        }

        try {
            $session = new Session(id: (int)$id);
            $session->update($data);
            $session->save();
            return $this->sendResponse(200, 'success', $session->jsonSerialize(), 'Session mise à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE (supprimer une session).
     *
     * @param string|null $id Identifiant de la session
     * @return array Réponse JSON
     */
    private function handleDelete(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la session requis');
        }

        try {
            $session = new Session(id: (int)$id);
            $session->delete();
            return $this->sendResponse(204, 'success', null, 'Session supprimée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Session non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Vérifie si une valeur existe dans une table.
     *
     * @param string $table Nom de la table
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur à vérifier
     * @return bool
     */
    private function existsInTable(string $table, string $column, $value): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchColumn() > 0;
    }

}