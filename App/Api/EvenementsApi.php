<?php
declare(strict_types=1);

namespace App\Api;

use App\Database\Types\Evenement;
use PDOException;
use InvalidArgumentException;

class EvenementsApi extends APIHandler
{
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                return $this->handleGet($id);
            case 'POST':
                $this->requirePermission('EvenementsApi', 'write');
                return $this->handlePost();
            case 'PUT':
                $this->requirePermission('EvenementsApi', 'write');
                return $this->handlePut($id);
            case 'DELETE':
                $this->requirePermission('EvenementsApi', 'delete');
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    private function handleGet(?string $id): array
    {
        if ($id !== null && is_numeric($id)) {
            try {
                $evenement = new Evenement(id: (int)$id);
                return $this->sendResponse(200, 'success', $evenement->jsonSerialize());
            } catch (PDOException $e) {
                return $this->sendResponse(404, 'error', null, 'Événement non trouvé: ' . $e->getMessage());
            }
        }

        try {
            $queryParams = $_GET;
            $dateDebut = $queryParams['date_debut'] ?? '';
            $evenements = Evenement::search($this->pdo, $dateDebut);
            return $this->sendResponse(200, 'success', array_values($evenements));
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    private function handlePost(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom'], $data['date_debut'], $data['date_fin'])) {
            return $this->sendResponse(400, 'error', null, 'nom, date_debut, date_fin requis');
        }

        // Vérifier que l'utilisateur est admin
        if ($this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Seul un admin peut créer un événement');
        }

        try {
            $evenement = new Evenement(
                nom: $data['nom'],
                description: $data['description'] ?? null,
                dateDebut: $data['date_debut'],
                dateFin: $data['date_fin'],
                idLieu: isset($data['id_lieu']) ? (int)$data['id_lieu'] : null,
                regleRecurrence: $data['regle_recurrence'] ?? null,
                exceptions: $data['exceptions'] ?? null
            );
            $evenement->save();

            return $this->sendResponse(201, 'success', $evenement->jsonSerialize(), 'Événement créé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handlePut(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de l’événement requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            return $this->sendResponse(400, 'error', null, 'Données requises');
        }

        // Vérifier que l'utilisateur est admin
        if ($this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Seul un admin peut modifier un événement');
        }

        try {
            $evenement = new Evenement(id: (int)$id);

            if (isset($data['nom'])) {
                $evenement->setNom($data['nom']);
            }
            if (isset($data['description'])) {
                $evenement->setDescription($data['description']);
            }
            if (isset($data['date_debut'])) {
                $evenement->setDateDebut($data['date_debut']);
            }
            if (isset($data['date_fin'])) {
                $evenement->setDateFin($data['date_fin']);
            }
            if (isset($data['id_lieu'])) {
                $evenement->setLieu($data['id_lieu'] !== null ? (int)$data['id_lieu'] : null);
            }
            if (isset($data['regle_recurrence'])) {
                $evenement->setRegleRecurrence($data['regle_recurrence']);
            }
            if (isset($data['exceptions'])) {
                $evenement->setExceptions($data['exceptions']);
            }

            $evenement->save();
            return $this->sendResponse(200, 'success', $evenement->jsonSerialize(), 'Événement mis à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Événement non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleDelete(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de l’événement requis');
        }

        // Vérifier que l'utilisateur est admin
        if ($this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Seul un admin peut supprimer un événement');
        }

        try {
            $evenement = new Evenement(id: (int)$id);
            $evenement->delete();
            return $this->sendResponse(204, 'success', null, 'Événement supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Événement non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }
}