<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Types\Utilisateur;
use App\Database\Types\TypeUtilisateur;
use App\Database\Types\Jeu;
use App\Database\Types\Lieu;
use App\Database\Types\Genre;
use Exception;

require_once __DIR__ ."/../Utils/helpers.php";

/**
 * Contrôleur pour la gestion de l'interface d'administration.
 */
class AdminController extends BaseController
{
    /**
     * Affiche la page d'administration principale.
     */
    public function index()
    {
        try {
            // Vérifier que l'utilisateur est connecté et est administrateur
            $currentUser = $this->getAuthenticatedUser();
            if (!$currentUser || $currentUser->getTypeUtilisateur() !== TypeUtilisateur::Administrateur) {
                $this->redirectToLogin();
                return;
            }

            // Préparer les données pour la vue
            $data = [
                'title' => 'Administration - CROM',
                'currentUser' => $currentUser,
                'users' => $this->getUsersData(),
                'games' => $this->getGamesData(),
                'locations' => $this->getLocationsData(),
                'genres' => $this->getGenresData(),
                'stats' => $this->getStatistics(),

                'modules' => [
                    'admin.js'
                ]
            ];

            return $this->render('pages.admin', $data);
        } catch (Exception $e) {
            error_log('Erreur dans AdminController::index: ' . $e->getMessage());
            return $this->render('errors.500', ['error' => 'Erreur interne du serveur']);
        }
    }

    /**
     * Récupère l'utilisateur actuellement authentifié via JWT cookie
     */
    private function getAuthenticatedUser(): ?Utilisateur
    {
        try {
            $user_id = getCurrentUserId();
            if (!$user_id) {
                return null;
            }
            return new Utilisateur($user_id);
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération de l\'utilisateur: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les données des utilisateurs pour l'administration.
     */
    private function getUsersData(): array
    {
        try {
            return Utilisateur::search($this->pdo);
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les données des jeux pour l'administration.
     */
    private function getGamesData(): array
    {
        try {
            return Jeu::search($this->pdo);
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des jeux: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les données des lieux pour l'administration.
     */
    private function getLocationsData(): array
    {
        try {
            return Lieu::search($this->pdo);
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des lieux: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les genres disponibles.
     */
    private function getGenresData(): array
    {
        try {
            return Genre::search($this->pdo);
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des genres: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les statistiques générales pour le tableau de bord admin.
     */
    private function getStatistics(): array
    {
        try {
            $stats = [];
            
            // Nombre total d'utilisateurs
            $stmt = $this->pdo->query('SELECT COUNT(*) as total FROM utilisateurs');
            $stats['totalUsers'] = $stmt->fetch()['total'] ?? 0;
            
            // Nombre d'utilisateurs actifs (connectés dans les 30 derniers jours)
            $stmt = $this->pdo->query('SELECT COUNT(*) as active FROM utilisateurs WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
            $stats['activeUsers'] = $stmt->fetch()['active'] ?? 0;
            
            // Nombre total de jeux
            $stmt = $this->pdo->query('SELECT COUNT(*) as total FROM jeux');
            $stats['totalGames'] = $stmt->fetch()['total'] ?? 0;
            
            // Nombre total d'activités
            $stmt = $this->pdo->query('SELECT COUNT(*) as total FROM activites WHERE etat != "SUPPRIMER"');
            $stats['totalActivities'] = $stmt->fetch()['total'] ?? 0;
            
            // Nombre total de sessions
            $stmt = $this->pdo->query('SELECT COUNT(*) as total FROM sessions');
            $stats['totalSessions'] = $stmt->fetch()['total'] ?? 0;
            
            // Nombre total de lieux
            $stmt = $this->pdo->query('SELECT COUNT(*) as total FROM lieux');
            $stats['totalLocations'] = $stmt->fetch()['total'] ?? 0;

            return $stats;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return [
                'totalUsers' => 0,
                'activeUsers' => 0,
                'totalGames' => 0,
                'totalActivities' => 0,
                'totalSessions' => 0,
                'totalLocations' => 0
            ];
        }
    }

    /**
     * Redirige vers la page de connexion si l'utilisateur n'est pas autorisé.
     */
    private function redirectToLogin(): void
    {
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
