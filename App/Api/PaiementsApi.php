<?php
declare(strict_types=1);

namespace App\Api;

use App\Database\Types\PaiementsHelloasso;
use App\Database\Types\NotificationsHelloasso;
use PDOException;
use InvalidArgumentException;

class PaiementsApi extends APIHandler
{
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $segments = array_values(array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))));

        // Gestion des endpoints notifications
        if (isset($segments[3]) && $segments[3] === 'notifications' && isset($segments[4]) && $segments[4] === 'helloasso') {
            if ($method === 'POST') {
                return $this->handlePostNotification();
            }
            return $this->sendResponse(405, 'error', null, 'Méthode non autorisée pour les notifications');
        }

        // Gestion des endpoints paiements
        switch ($method) {
            case 'GET':
                $this->requirePermission('PaiementsApi', 'read');
                return $this->handleGet($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    private function handleGet(?string $id): array
    {
        // Vérifier que l'utilisateur est admin
        if ($this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Seul un admin peut accéder aux paiements');
        }

        if ($id !== null) {
            try {
                $paiement = new PaiementsHelloasso(id: $id);
                return $this->sendResponse(200, 'success', $paiement->jsonSerialize());
            } catch (PDOException $e) {
                return $this->sendResponse(404, 'error', null, 'Paiement non trouvé: ' . $e->getMessage());
            }
        }

        try {
            $queryParams = $_GET;
            $idUtilisateur = $queryParams['utilisateur_id'] ?? '';
            if ($this->user['sub'] !== $idUtilisateur && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
                $idUtilisateur = $this->user['sub'];
            }
            $statut = $queryParams['statut'] ?? '';
            $paiements = PaiementsHelloasso::search($this->pdo, $idUtilisateur, $statut);
            return $this->sendResponse(200, 'success', array_values($paiements));
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    private function handlePostNotification(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'], $data['type_evenement'], $data['date_evenement'], $data['donnees'])) {
            return $this->sendResponse(400, 'error', null, 'id, type_evenement, date_evenement, donnees requis');
        }

        try {
            // Vérifier si la notification existe déjà
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notifications_helloasso WHERE id = :id');
            $stmt->execute(['id' => $data['id']]);
            if ($stmt->fetchColumn() > 0) {
                return $this->sendResponse(409, 'error', null, 'Notification avec cet ID existe déjà');
            }

            if (!$this->verifySignature()) {
                return $this->sendResponse(401, 'error', null, 'Signature invalide');
            }

            $notification = new NotificationsHelloasso(
                id: $data['id'],
                typeEvenement: $data['type_evenement'],
                dateEvenement: $data['date_evenement'],
                donnees: json_encode($data['donnees']),
                dateReception: date('Y-m-d H:i:s'),
                traite: false
            );
            $notification->save();

            // Si les données contiennent un paiement, créer un paiement
            if (isset($data['donnees']['montant'], $data['donnees']['devise'])) {
                $paiementData = $data['donnees'];
                $paiement = new PaiementsHelloasso(
                    id: generateUUID(),
                    notificationOuId: $notification,
                    utilisateurOuId: $paiementData['id_utilisateur'] ?? null,
                    typePaiement: $paiementData['type_paiement'] ?? null,
                    nom: $paiementData['nom'] ?? null,
                    montant: (float)$paiementData['montant'],
                    devise: $paiementData['devise'],
                    dateEcheance: $paiementData['date_echeance'] ?? null,
                    statut: $paiementData['statut'] ?? null,
                    metadonnees: isset($paiementData['metadonnees']) ? json_encode($paiementData['metadonnees']) : null
                );
                $paiement->save();
            }

            return $this->sendResponse(201, 'success', $notification->jsonSerialize(), 'Notification enregistrée avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de l’enregistrement: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    private function verifySignature(): bool
    {
        $secret = getenv('HELLOASSO_WEBHOOK_SECRET');
        $signature = $_SERVER['HTTP_X_HELLOASSO_SIGNATURE'] ?? '';
        $payload = file_get_contents('php://input');
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}