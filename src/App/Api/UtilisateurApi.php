<?php

declare(strict_types=1);

namespace App\Api;

use App\Database\Database;
use App\Database\Types\Utilisateur;
use App\Database\Types\CreneauxUtilisateur;
use App\Database\Types\Sexe;
use App\Database\Types\TypeUtilisateur;
use App\Database\Types\TypeCreneau;
use Firebase\JWT\JWT;
use PDO;
use PDOException;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use DateTime;

require_once __DIR__ . '/../vendor/autoload.php';

class UtilisateurApi extends APIHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getConnection();
    }

    /**
     * Gère les requêtes HTTP pour la ressource Utilisateur.
     *
     * @param string|null $id Identifiant de l'utilisateur ou action spécifique
     * @return array Réponse JSON
     */
    public function handle(?string $id): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST' && $id === 'inscription') {
            return $this->handleInscription();
        }

        if ($method === 'POST' && $id === 'connexion') {
            return $this->handleLogin();
        }

        if ($method === 'POST' && $id === 'refresh') {
            return $this->handleRefresh();
        }

        // TODO: Ajoutez un endpoint /api/utilisateurs/deconnexion pour invalider les refresh tokens
        // TODO: Implémentez un script pour supprimer les refresh tokens expirés de la base.

        if (preg_match('/^([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})\/creneaux$/', $id, $matches)) {
            $userId = $matches[1];
            if ($method === 'GET') {
                $this->requirePermission('UtilisateurApi', 'read');
                return $this->handleGetCreneaux($userId);
            }
            if ($method === 'POST') {
                $this->requirePermission('UtilisateurApi', 'write');
                return $this->handlePostCreneau($userId);
            }
            if ($method === 'PATCH') {
                $this->requirePermission('UtilisateurApi', 'write');
                return $this->handlePatchCreneau($userId);
            }
        }

        if (preg_match('/^([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})\/creneaux\/(\d+)$/', $id, $matches)) {
            $userId = $matches[1];
            $creneauId = $matches[2];
            if ($method === 'DELETE') {
                $this->requirePermission('UtilisateurApi', 'delete');
                return $this->handleDeleteCreneau($userId, $creneauId);
            }
        }

        switch ($method) {
            case 'GET':
                $this->requirePermission('UtilisateurApi', 'read');
                return $this->handleGet($id);
            case 'PUT':
                $this->requirePermission('UtilisateurApi', 'write');
                return $this->handlePut($id);
            case 'DELETE':
                $this->requirePermission('UtilisateurApi', 'delete');
                return $this->handleDelete($id);
            default:
                return $this->sendResponse(405, 'error', null, 'Méthode non autorisée');
        }
    }

    /**
     * Gère l'inscription d'un nouvel utilisateur.
     *
     * @return array Réponse JSON
     */
    private function handleInscription(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['prenom'], $data['nom'], $data['login'], $data['mot_de_passe'], $data['sexe'])) {
            return $this->sendResponse(400, 'error', null, 'Prénom, nom, login, mot de passe et sexe sont requis');
        }

        try {
            $prenom = trim($data['prenom']);
            $nom = trim($data['nom']);
            $login = trim($data['login']);
            $motDePasse = $data['mot_de_passe'];
            $sexe = Sexe::tryFrom($data['sexe']);
            $email = isset($data['email']) ? trim($data['email']) : null;
            $dateDeNaissance = isset($data['date_de_naissance']) ? DateTime::createFromFormat('Y-m-d', $data['date_de_naissance']) : null;
            $idDiscord = isset($data['id_discord']) ? trim($data['id_discord']) : null;
            $pseudonyme = isset($data['pseudonyme']) ? trim($data['pseudonyme']) : null;
            $typeUtilisateur = isset($data['type_utilisateur']) ? TypeUtilisateur::tryFrom($data['type_utilisateur']) : TypeUtilisateur::Inscrit;

            if ($sexe === null) {
                return $this->sendResponse(400, 'error', null, 'Sexe invalide');
            }

            if ($typeUtilisateur === null) {
                return $this->sendResponse(400, 'error', null, 'Type d\'utilisateur invalide');
            }

            // Vérifier les longueurs et formats
            if (strlen($prenom) > 255 || strlen($nom) > 255 || strlen($login) > 255) {
                return $this->sendResponse(400, 'error', null, 'Les champs prénom, nom et login ne doivent pas dépasser 255 caractères');
            }

            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->sendResponse(400, 'error', null, 'Email invalide');
            }

            if ($pseudonyme && strlen($pseudonyme) > 255) {
                return $this->sendResponse(400, 'error', null, 'Le pseudonyme ne doit pas dépasser 255 caractères');
            }

            $utilisateur = new Utilisateur(
                prenom: $prenom,
                nom: $nom,
                nomUtilisateur: $login,
                motDePasse: $motDePasse,
                sexe: $sexe,
                email: $email,
                dateDeNaissance: $dateDeNaissance,
                idDiscord: $idDiscord,
                pseudonyme: $pseudonyme,
                typeUtilisateur: $typeUtilisateur,
                dateInscription: new DateTime()
            );

            $utilisateur->save();

            $payload = [
                'iat' => time(),
                'exp' => time() + 3600, // 1 heure
                'sub' => $utilisateur->getId(),
                'type_utilisateur' => $utilisateur->getTypeUtilisateur()->value
            ];
            $jwt = JWT::encode($payload, 'votre_secret_jwt_ici', 'HS256');

            return $this->sendResponse(201, 'success', [
                'token' => $jwt,
                'utilisateur' => $utilisateur->jsonSerialize()
            ], 'Utilisateur créé avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Login, email ou ID Discord déjà utilisé');
            }
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère la connexion d'un utilisateur.
     *
     * @return array Réponse JSON
     */
    private function handleLogin(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['login'], $data['mot_de_passe'])) {
            return $this->sendResponse(400, 'error', null, 'Login et mot de passe requis');
        }

        try {
            $utilisateur = Utilisateur::findByLogin($this->pdo, $data['login']);
            if (!$utilisateur || !password_verify($data['mot_de_passe'], $utilisateur->getMotDePasse())) {
                return $this->sendResponse(401, 'error', null, 'Identifiants invalides');
            }

            $payload = [
                'iat' => time(),
                'exp' => time() + 3600, // 1 heure
                'sub' => $utilisateur->getId(),
                'type_utilisateur' => $utilisateur->getTypeUtilisateur()->value
            ];
            $jwt = JWT::encode($payload, 'votre_secret_jwt_ici', 'HS256');

            $response = [
                'token' => $jwt,
                'utilisateur' => $utilisateur->jsonSerialize()
            ];

            // Gérer "Rester connecté"
            if (isset($data['keep_logged_in']) && $data['keep_logged_in']) {
                $refreshToken = bin2hex(random_bytes(32));
                $refreshTokenId = Uuid::uuid4()->toString();
                $expiration = new \DateTime('+30 days');

                $stmt = $this->pdo->prepare(
                    'INSERT INTO refresh_tokens (id, id_utilisateur, token, date_expiration) 
                     VALUES (:id, :id_utilisateur, :token, :date_expiration)'
                );
                $stmt->execute([
                    'id' => $refreshTokenId,
                    'id_utilisateur' => $utilisateur->getId(),
                    'token' => $refreshToken,
                    'date_expiration' => $expiration->format('Y-m-d H:i:s')
                ]);

                $response['refresh_token'] = $refreshToken;
            }

            return $this->sendResponse(200, 'success', $response, 'Connexion réussie');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur serveur: ' . $e->getMessage());
        }
    }

    /**
     * Gère le rafraîchissement du token JWT.
     *
     * @return array Réponse JSON
     */
    private function handleRefresh(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['refresh_token'])) {
            return $this->sendResponse(400, 'error', null, 'Refresh token requis');
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM refresh_tokens WHERE token = :token AND date_expiration > NOW()'
            );
            $stmt->execute(['token' => $data['refresh_token']]);
            $refreshToken = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$refreshToken) {
                return $this->sendResponse(401, 'error', null, 'Refresh token invalide ou expiré');
            }

            $utilisateur = new Utilisateur($refreshToken['id_utilisateur']);
            if (!$utilisateur) {
                return $this->sendResponse(401, 'error', null, 'Utilisateur non trouvé');
            }

            $payload = [
                'iat' => time(),
                'exp' => time() + 3600,
                'sub' => $utilisateur->getId(),
                'type_utilisateur' => $utilisateur->getTypeUtilisateur()->value
            ];
            $jwt = JWT::encode($payload, 'votre_secret_jwt_ici', 'HS256');

            return $this->sendResponse(200, 'success', [
                'token' => $jwt,
                'utilisateur' => $utilisateur->jsonSerialize()
            ], 'Token renouvelé');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur serveur: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes GET pour récupérer les informations d'un utilisateur.
     *
     * @param string|null $id Identifiant UUID de l'utilisateur
     * @return array Réponse JSON
     */
    private function handleGet(?string $id): array
    {
        if ($id === null || !isValidUuid($id)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur requis et doit être un UUID valide');
        }

        // Vérifier si l'utilisateur connecté peut accéder à ces données
        if ($this->user['sub'] !== $id && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Vous ne pouvez accéder qu\'à vos propres informations');
        }

        try {
            $utilisateur = new Utilisateur($id);
            return $this->sendResponse(200, 'success', $utilisateur->jsonSerialize());
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Utilisateur non trouvé: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PUT pour mettre à jour un utilisateur.
     *
     * @param string|null $id Identifiant UUID de l'utilisateur
     * @return array Réponse JSON
     */
    private function handlePut(?string $id): array
    {
        if ($id === null || !isValidUuid($id)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur requis et doit être un UUID valide');
        }

        // Vérifier si l'utilisateur connecté peut modifier cet utilisateur
        if ($this->user['sub'] !== $id && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Vous ne pouvez modifier qu\'à votre propre profil');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            return $this->sendResponse(400, 'error', null, 'Données requises pour la mise à jour');
        }

        try {
            $utilisateur = new Utilisateur($id);

            if (isset($data['prenom'])) {
                $utilisateur->setPrenom(trim($data['prenom']));
            }
            if (isset($data['nom'])) {
                $utilisateur->setNom(trim($data['nom']));
            }
            if (isset($data['email'])) {
                $utilisateur->setEmail(trim($data['email']));
            }
            if (isset($data['login'])) {
                $utilisateur->setNomUtilisateur(trim($data['login']));
            }
            if (isset($data['date_de_naissance'])) {
                $dateDeNaissance = DateTime::createFromFormat('Y-m-d', $data['date_de_naissance']);
                if ($dateDeNaissance === false) {
                    return $this->sendResponse(400, 'error', null, 'Format de date de naissance invalide (Y-m-d)');
                }
                $utilisateur->setDateDeNaissance($dateDeNaissance);
            }
            if (isset($data['sexe'])) {
                $sexe = Sexe::tryFrom($data['sexe']);
                if ($sexe === null) {
                    return $this->sendResponse(400, 'error', null, 'Sexe invalide');
                }
                $utilisateur->setSexe($sexe);
            }
            if (isset($data['id_discord'])) {
                $utilisateur->setIdDiscord(trim($data['id_discord']));
            }
            if (isset($data['pseudonyme'])) {
                $utilisateur->setPseudonyme(trim($data['pseudonyme']));
            }
            if (isset($data['mot_de_passe'])) {
                $utilisateur->setMotDePasse($data['mot_de_passe']);
            }
            if (isset($data['type_utilisateur']) && $this->user['type_utilisateur'] === 'ADMINISTRATEUR') {
                $typeUtilisateur = TypeUtilisateur::tryFrom($data['type_utilisateur']);
                if ($typeUtilisateur === null) {
                    return $this->sendResponse(400, 'error', null, 'Type d\'utilisateur invalide');
                }
                $utilisateur->setTypeUtilisateur($typeUtilisateur);
            }

            $utilisateur->save();
            return $this->sendResponse(200, 'success', $utilisateur->jsonSerialize(), 'Utilisateur mis à jour avec succès');
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return $this->sendResponse(409, 'error', null, 'Login, email ou ID Discord déjà utilisé');
            }
            return $this->sendResponse(404, 'error', null, 'Utilisateur non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE pour supprimer un utilisateur.
     *
     * @param string|null $id Identifiant UUID de l'utilisateur
     * @return array Réponse JSON
     */
    private function handleDelete(?string $id): array
    {
        if ($id === null || !isValidUuid($id)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur requis et doit être un UUID valide');
        }

        try {
            $utilisateur = new Utilisateur($id);
            $utilisateur->delete();
            return $this->sendResponse(204, 'success', null, 'Utilisateur supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Utilisateur non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes GET pour récupérer les créneaux d'un utilisateur.
     *
     * @param string $userId Identifiant UUID de l'utilisateur
     * @return array Réponse JSON
     */
    private function handleGetCreneaux(string $userId): array
    {
        if (!isValidUuid($userId)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur requis et doit être un UUID valide');
        }

        // Vérifier si l'utilisateur connecté peut accéder à ces données
        if ($this->user['sub'] !== $userId && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Vous ne pouvez accéder qu\'à vos propres créneaux');
        }

        try {
            $creneaux = CreneauxUtilisateur::search($this->pdo, $userId);
            return $this->sendResponse(200, 'success', $creneaux);
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de la récupération des créneaux: ' . $e->getMessage());
        }
    }

    /**
     * Gère les requêtes POST pour ajouter un créneau à un utilisateur.
     *
     * @param string $userId Identifiant UUID de l'utilisateur
     * @return array Réponse JSON
     */
    private function handlePostCreneau(string $userId): array
    {
        if (!isValidUuid($userId)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur requis et doit être un UUID valide');
        }

        // Vérifier si l'utilisateur connecté peut ajouter un créneau pour cet utilisateur
        if ($this->user['sub'] !== $userId && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Vous ne pouvez ajouter des créneaux qu\'à votre propre profil');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['type_creneau'], $data['date_heure_debut'], $data['date_heure_fin'], $data['est_recurrant'])) {
            return $this->sendResponse(400, 'error', null, 'Type de créneau, date de début, date de fin et statut récurrent requis');
        }

        try {
            $typeCreneau = TypeCreneau::tryFrom($data['type_creneau']);
            if ($typeCreneau === null) {
                return $this->sendResponse(400, 'error', null, 'Type de créneau invalide');
            }

            $creneau = new CreneauxUtilisateur(
                utilisateurOuId: $userId,
                typeCreneau: $typeCreneau,
                dateHeureDebut: $data['date_heure_debut'],
                dateHeureFin: $data['date_heure_fin'],
                estRecurrant: $data['est_recurrant'],
                regleRecurrence: $data['regle_recurrence'] ?? null
            );

            $creneau->save();
            return $this->sendResponse(201, 'success', $creneau->jsonSerialize(), 'Créneau ajouté avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(500, 'error', null, 'Erreur lors de l\'ajout du créneau: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes PATCH pour mettre à jour un créneau d'un utilisateur.
     *
     * @param string $userId Identifiant UUID de l'utilisateur
     * @return array Réponse JSON
     */
    private function handlePatchCreneau(string $userId): array
    {
        if (!isValidUuid($userId)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur requis et doit être un UUID valide');
        }

        // Vérifier si l'utilisateur connecté peut modifier ce créneau
        if ($this->user['sub'] !== $userId && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Vous ne pouvez modifier que vos propres créneaux');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id_creneau'])) {
            return $this->sendResponse(400, 'error', null, 'ID du créneau requis pour la mise à jour');
        }

        try {
            $creneau = new CreneauxUtilisateur(id: (int)$data['id_creneau']);
            if ($creneau->getUtilisateur()->getId() !== $userId) {
                return $this->sendResponse(403, 'error', null, 'Le créneau n\'appartient pas à cet utilisateur');
            }

            if (isset($data['type_creneau'])) {
                $typeCreneau = TypeCreneau::tryFrom($data['type_creneau']);
                if ($typeCreneau === null) {
                    return $this->sendResponse(400, 'error', null, 'Type de créneau invalide');
                }
                $creneau->setTypeCreneau($typeCreneau);
            }

            if (isset($data['date_heure_debut'])) {
                $creneau->setDateHeureDebut($data['date_heure_debut']);
            }

            if (isset($data['date_heure_fin'])) {
                $creneau->setDateHeureFin($data['date_heure_fin']);
            }

            if (isset($data['est_recurrant'])) {
                $creneau->setEstRecurrant($data['est_recurrant']);
            }

            if (isset($data['regle_recurrence'])) {
                $creneau->setRegleRecurrence($data['regle_recurrence']);
            }

            $creneau->save();
            return $this->sendResponse(200, 'success', $creneau->jsonSerialize(), 'Créneau mis à jour avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Créneau non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    /**
     * Gère les requêtes DELETE pour supprimer un créneau d'un utilisateur.
     *
     * @param string $userId Identifiant UUID de l'utilisateur
     * @param string $creneauId Identifiant du créneau
     * @return array Réponse JSON
     */
    private function handleDeleteCreneau(string $userId, string $creneauId): array
    {
        if (!isValidUuid($userId) || !is_numeric($creneauId)) {
            return $this->sendResponse(400, 'error', null, 'ID utilisateur et ID créneau requis');
        }

        // Vérifier si l'utilisateur connecté peut supprimer ce créneau
        if ($this->user['sub'] !== $userId && $this->user['type_utilisateur'] !== 'ADMINISTRATEUR') {
            return $this->sendResponse(403, 'error', null, 'Vous ne pouvez supprimer que vos propres créneaux');
        }

        try {
            $creneau = new CreneauxUtilisateur(id: (int)$creneauId);
            if ($creneau->getUtilisateur()->getId() !== $userId) {
                return $this->sendResponse(403, 'error', null, 'Le créneau n\'appartient pas à cet utilisateur');
            }
            $creneau->delete();
            return $this->sendResponse(204, 'success', null, 'Créneau supprimé avec succès');
        } catch (PDOException $e) {
            return $this->sendResponse(404, 'error', null, 'Créneau non trouvé: ' . $e->getMessage());
        } catch (InvalidArgumentException $e) {
            return $this->sendResponse(400, 'error', null, $e->getMessage());
        }
    }

    
}