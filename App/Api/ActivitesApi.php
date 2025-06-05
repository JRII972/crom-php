<?php
declare(strict_types=1);

namespace App\Api;

use App\Database\Types\Activite;
use App\Database\Types\MembreActivite;
use App\Database\Types\TypeActivite;
use App\Database\Types\TypeCampagne;
use App\Utils\Image;
use PDOException;
use InvalidArgumentException;

require_once __DIR__ . '/../Database/Types/Activite.php';

class ActivitesApi extends APIHandler
{
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $segments = array_values(array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))));

        // Gestion des endpoints membres
        if (isset($segments[3]) && $segments[3] === 'membres') {
            if ($method === 'POST' && count($segments) === 4) {
                $this->requirePermission('ActiviteApi', 'write');
                return $this->handleAddMember($id);
            }
            if ($method === 'DELETE' && isset($segments[4])) {
                $this->requirePermission('ActiviteApi', 'delete');
                return $this->handleRemoveMember($id, $segments[4]);
            }
            return $this->sendResponse(405, 'error', null, 'Méthode non autorisée pour les membres');
        }

        // Gestion des endpoints principaux
        switch ($method) {
            case 'GET':
                return $this->handleGet($id);
            case 'POST':
                $this->requirePermission('ActiviteApi', 'write');
                return $this->handlePost();
            case 'PUT':
                $this->requirePermission('ActiviteApi', 'write');
                return $this->handlePut($id);
            case 'PATCH':
                $this->requirePermission('ActiviteApi', 'write');
                return $this->handlePatch($id);
            case 'DELETE':
                $this->requirePermission('ActiviteApi', 'delete');
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    private function handleGet(?string $id): array
    {
        if ($id !== null && is_numeric($id)) {
            try {
                $activite = new Activite(id: (int)$id);
                return $this->sendResponse(200, 'success', $activite->jsonSerialize());
            } catch (PDOException $e) {
                return $this->sendResponse(404, 'error', null, 'Activite non trouvée: ' . $e->getMessage());
            }
        }

        try {
            $queryParams = $_GET;
            $keyword = $queryParams['q'] ?? '';
            $typeActivite = $queryParams['type_activite'] ?? '';
            $jeuId = isset($queryParams['jeu_id']) ? (int)$queryParams['jeu_id'] : 0;
            $maitreJeu = $queryParams['mj'] ?? '';
            $placeRestante = isset($queryParams['place_restante']) ? filter_var($queryParams['place_restante'], FILTER_VALIDATE_BOOLEAN) : null;
            $verrouille = isset($queryParams['verrouille']) ? filter_var($queryParams['verrouille'], FILTER_VALIDATE_BOOLEAN) : null;
            $order = $queryParams['order'] ?? '';
            
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

            $activites = Activite::search(
                $this->pdo,
                $keyword,
                $jeuId,
                $maitreJeu,
                $typeActivite,
                $categories,
                $jours
            );

            // Filtrer par place_restante et verrouille
            $filteredActivites = array_filter($activites, function ($activite) use ($placeRestante, $verrouille) {
                $activiteObj = new Activite(id: (int)$activite['id']);
                $matchesPlace = $placeRestante === null || ($placeRestante && $activiteObj->restePlace());
                $matchesVerrouille = $verrouille === null || ($verrouille === $activite['verrouille']);
                return $matchesPlace && $matchesVerrouille;
            });

            // Trier les résultats
            if ($order === 'date_creation') {
                usort($filteredActivites, fn($a, $b) => strcmp($b['date_creation'], $a['date_creation']));
            } elseif ($order === 'prochaine_session') {
                usort($filteredActivites, function ($a, $b) {
                    $activiteA = new Activite(id: (int)$a['id']);
                    $activiteB = new Activite(id: (int)$b['id']);
                    $sessionsA = $activiteA->getSessions();
                    $sessionsB = $activiteB->getSessions();
                    $nextSessionA = $this->getNextSessionDate($sessionsA);
                    $nextSessionB = $this->getNextSessionDate($sessionsB);
                    return $nextSessionA <=> $nextSessionB;
                });
            }

            return $this->sendResponse(200, 'success', array_values($filteredActivites));
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    private function getNextSessionDate(array $sessions): ?string
    {
        $now = new \DateTime();
        $nextDate = null;
        foreach ($sessions as $session) {
            $sessionDate = \DateTime::createFromFormat('Y-m-d H:i:s', $session->getDateSession() . ' ' . $session->getHeureDebut());
            if ($sessionDate >= $now && (!$nextDate || $sessionDate < $nextDate)) {
                $nextDate = $sessionDate;
            }
        }
        return $nextDate ? $nextDate->format('Y-m-d H:i:s') : null;
    }

    private function handlePost(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_jeu'], $data['id_maitre_jeu'], $data['type_activite'], $data['nom'])) {
            return $this->sendResponse(400, 'error', null, 'id_jeu, id_maitre_jeu, type_activite et nom requis');
        }

        try {
            $typeActivite = TypeActivite::tryFrom($data['type_activite']);
            if ($typeActivite === null) {
                return $this->sendResponse(400, 'error', null, 'Type de activite invalide');
            }

            $typeCampagne = isset($data['type_campagne']) ? TypeCampagne::tryFrom($data['type_campagne']) : null;
            if ($typeActivite === TypeActivite::Campagne && $typeCampagne === null) {
                return $this->sendResponse(400, 'error', null, 'type_campagne requis pour une campagne');
            }

            // Vérifier que l'utilisateur est le maître du jeu ou admin
            if ($this->user['sub'] !== $data['id_maitre_jeu'] && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
                return $this->sendResponse(403, 'error', null, 'Seul le maître du jeu ou un admin peut créer une activite');
            }

            $image = isset($data['image']) ? $data['image'] : null;
            if ($image && !is_array($image) && !is_string($image)) {
                return $this->sendResponse(400, 'error', null, 'Image invalide');
            }

            $activite = new Activite(
                nom: trim($data['nom']),
                jeuOuId: (int)$data['id_jeu'],
                maitreJeuOuId: $data['id_maitre_jeu'], //$this->user['sub']
                typeActivite: $typeActivite,
                typeCampagne: $typeCampagne,
                descriptionCourte: $data['description_courte'] ?? null,
                description: $data['description'] ?? null,
                nombreMaxJoueurs: isset($data['nombre_max_joueurs']) ? (int)$data['nombre_max_joueurs'] : 0,
                maxJoueursSession: isset($data['max_joueurs_session']) ? (int)$data['max_joueurs_session'] : 5,
                image: $image,
                texteAltImage: $data['texte_alt_image'] ?? null
            );
            $activite->save();

            return $this->sendResponse(201, 'success', $activite->jsonSerialize(), 'Activite créée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handlePut(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la activite requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['nom'])) {
            return $this->sendResponse(400, 'error', null, 'Nom requis');
        }

        try {   
            $activite = new Activite(id: (int)$id);
            $this->verifyMasterOrAdmin($activite);

            $activite->setNom(trim($data['nom']));
            if (isset($data['id_jeu'])) {
                $activite->setJeu((int)$data['id_jeu']);
            }
            if (isset($data['id_maitre_jeu'])) {
                $activite->setMaitreJeu($data['id_maitre_jeu']);
            }
            if (isset($data['type_activite'])) {
                $typeActivite = TypeActivite::tryFrom($data['type_activite']);
                if ($typeActivite === null) {
                    return $this->sendResponse(400, 'error', null, 'Type de activite invalide');
                }
                $activite->setTypeActivite($typeActivite);
            }
            if (isset($data['type_campagne'])) {
                $typeCampagne = TypeCampagne::tryFrom($data['type_campagne']);
                $activite->setTypeCampagne($typeCampagne);
            }
            if (isset($data['description_courte'])) {
                $activite->setDescriptionCourte($data['description_courte']);
            }
            if (isset($data['description'])) {
                $activite->setDescription($data['description']);
            }
            if (isset($data['nombre_max_joueurs'])) {
                $activite->setNombreMaxJoueurs((int)$data['nombre_max_joueurs']);
            }
            if (isset($data['max_joueurs_session'])) {
                $activite->setMaxJoueursSession((int)$data['max_joueurs_session']);
            }
            if (isset($data['image'])) {
                $activite->setImage($data['image']);
            }
            if (isset($data['texte_alt_image'])) {
                $activite->setTexteAltImage($data['texte_alt_image']);
            }

            $activite->save();
            return $this->sendResponse(200, 'success', $activite->jsonSerialize(), 'Activite mise à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Activite non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handlePatch(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la activite requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            return $this->sendResponse(400, 'error', null, 'Données requises');
        }

        try {
            $activite = new Activite(id: (int)$id);
            $this->verifyMasterOrAdmin($activite);

            if (isset($data['nom'])) {
                $activite->setNom(trim($data['nom']));
            }
            if (isset($data['id_jeu'])) {
                $activite->setJeu((int)$data['id_jeu']);
            }
            if (isset($data['id_maitre_jeu'])) {
                $activite->setMaitreJeu($data['id_maitre_jeu']);
            }
            if (isset($data['type_activite'])) {
                $typeActivite = TypeActivite::tryFrom($data['type_activite']);
                if ($typeActivite === null) {
                    return $this->sendResponse(400, 'error', null, 'Type de activite invalide');
                }
                $activite->setTypeActivite($typeActivite);
            }
            if (isset($data['type_campagne'])) {
                $typeCampagne = TypeCampagne::tryFrom($data['type_campagne']);
                $activite->setTypeCampagne($typeCampagne);
            }
            if (isset($data['description_courte'])) {
                $activite->setDescriptionCourte($data['description_courte']);
            }
            if (isset($data['description'])) {
                $activite->setDescription($data['description']);
            }
            if (isset($data['nombre_max_joueurs'])) {
                $activite->setNombreMaxJoueurs((int)$data['nombre_max_joueurs']);
            }
            if (isset($data['max_joueurs_session'])) {
                $activite->setMaxJoueursSession((int)$data['max_joueurs_session']);
            }
            if (isset($data['image'])) {
                $activite->setImage($data['image']);
            }
            if (isset($data['texte_alt_image'])) {
                $activite->setTexteAltImage($data['texte_alt_image']);
            }

            $activite->save();
            return $this->sendResponse(200, 'success', $activite->jsonSerialize(), 'Activite mise à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Activite non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleDelete(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la activite requis');
        }

        try {
            $activite = new Activite(id: (int)$id);
            $this->verifyMasterOrAdmin($activite);

            $activite->delete();
            return $this->sendResponse(204, 'success', null, 'Activite supprimée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Activite non trouvée: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleAddMember(?string $id): array
    {
        if ($id === null || !is_numeric($id)) {
            return $this->sendResponse(400, 'error', null, 'ID de la activite requis');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_utilisateur'])) {
            return $this->sendResponse(400, 'error', null, 'id_utilisateur requis');
        }

        try {
            $activite = new Activite(id: (int)$id);
            $this->verifyMasterOrAdmin($activite);

            if ($activite->getTypeActivite() !== TypeActivite::Campagne) {
                return $this->sendResponse(400, 'error', null, 'Les membres ne peuvent être ajoutés qu’à une campagne');
            }

            $membre = new MembreActivite($activite, $data['id_utilisateur']);
            $membre->save();

            return $this->sendResponse(201, 'success', $membre->jsonSerialize(), 'Membre ajouté avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(400, 'error', null, 'Erreur lors de l’ajout du membre: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function handleRemoveMember(?string $id, ?string $userId): array
    {
        if ($id === null || !is_numeric($id) || $userId === null) {
            return $this->sendResponse(400, 'error', null, 'ID de la activite et ID de l’utilisateur requis');
        }

        try {
            $activite = new Activite(id: (int)$id);
            $this->verifyMasterOrAdmin($activite); //FIXME: Permettre au joueur lui meme de se désincrire

            $membre = new MembreActivite($activite, $userId);
            $membre->delete();

            return $this->sendResponse(204, 'success', null, 'Membre retiré avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Membre ou activite non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function verifyMasterOrAdmin(Activite $activite): void
    {
        if ($this->user['sub'] !== $activite->getMaitreJeu()->getId() && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            throw new InvalidArgumentException('Seul le maître du jeu ou un admin peut effectuer cette action', 403);
        }
    }

    /**
     * Vérifie si une chaîne est au format JSON.
     *
     * @param string $string Chaîne à vérifier
     * @return bool True si la chaîne est au format JSON, false sinon
     */
    private function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}