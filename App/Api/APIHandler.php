<?php
declare(strict_types=1);

namespace App\Api;

use PDO;
use App\Database\Database;
use Exception;

class APIHandler
{
    protected PDO $pdo;
    protected ?array $user = null;

    // Mapping des rôles aux niveaux d'autorisation
    private const ROLE_LEVELS = [
        'NON_INSCRIT' => 0,
        'INSCRIT' => 10,
        'ADMINISTRATEUR' => 100
    ];

    // Permissions requises par classe et action
    private const PERMISSION_LEVELS = [
        'JeuApi' => [
            'read' => 0, // Tout le monde peut lire
            'write' => 100, // Seulement ADMIN
            'delete' => 100
        ],
        'ActiviteApi' => [
            'read' => 0,
            'write' => 10, // INSCRIT ou ADMIN
            'delete' => 10, // INSCRIT (si maître du jeu) ou ADMIN
        ],
        'UtilisateurApi' => [
            'read' => 10, // INSCRIT (soi-même) ou ADMIN
            'write' => 10,
            'delete' => 100
        ],
        // Ajouter d'autres classes (SessionsApi, LieuxApi, etc.)
    ];

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function setUser(?array $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?array
    {
        return $this->user;
    }

    protected function requireAuth(): void
    {
        if (!$this->user) {
            throw new Exception('Authentification requise', 401);
        }
    }

    /**
     * Vérifie si l'utilisateur a le niveau d'autorisation requis pour une action.
     *
     * @param string $class Nom de la classe (ex. JeuApi)
     * @param string $action Action (read, write, delete)
     * @throws Exception Si l'autorisation est insuffisante
     */
    protected function requirePermission(string $class, string $action): void
    {
        return; //TODO: enable permission
        $this->requireAuth();
        $requiredLevel = self::PERMISSION_LEVELS[$class][$action] ?? 100;
        $userLevel = self::ROLE_LEVELS[$this->user['type_utilisateur']] ?? 0;

        if ($userLevel < $requiredLevel) {
            throw new Exception('Privilèges insuffisants pour ' . $class . ':' . $action, 403);
        }
    }

    protected function sendResponse(int $status, string $statusMessage, $data = null, ?string $message = null): array
    {
        http_response_code($status);
        return [
            'status' => $statusMessage,
            'data' => $data,
            'message' => $message
        ];
    }
}