<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Gestionnaire centralisé de configuration des caches
 * 
 * Cette classe fournit une interface unique pour accéder aux configurations
 * de cache de toutes les classes App\Database\Types
 */
class CacheConfig
{
    private static ?array $config = null;
    private static ?string $environment = null;

    /**
     * Charge la configuration depuis le fichier config/cache.php
     */
    private static function loadConfig(): void
    {
        if (self::$config === null) {
            $configPath = __DIR__ . '/../../config/cache.php';
            if (!file_exists($configPath)) {
                throw new \RuntimeException('Fichier de configuration cache non trouvé : ' . $configPath);
            }
            self::$config = require $configPath;
        }
    }

    /**
     * Détermine l'environnement actuel
     */
    private static function getEnvironment(): string
    {
        if (self::$environment === null) {
            // Détection automatique de l'environnement
            if (defined('PHPUNIT_COMPOSER_INSTALL')) {
                self::$environment = 'testing';
            } elseif (isset($_ENV['APP_ENV'])) {
                self::$environment = $_ENV['APP_ENV'];
            } elseif (isset($_SERVER['APP_ENV'])) {
                self::$environment = $_SERVER['APP_ENV'];
            } elseif (defined('APP_ENV')) {
                self::$environment = APP_ENV;
            } else {
                // Par défaut, considérer comme production si pas de configuration spécifique
                self::$environment = (bool) ($_SERVER['HTTP_HOST'] ?? false) && 
                                   !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ? 
                                   'production' : 'development';
            }
        }
        return self::$environment;
    }

    /**
     * Vérifie si le cache est activé pour une classe donnée
     * 
     * @param string $className Nom de la classe (ex: 'Session', 'Activite')
     * @return bool True si le cache est activé
     */
    public static function isEnabled(string $className): bool
    {
        self::loadConfig();
        $env = self::getEnvironment();

        // Vérifier si le cache est forcément désactivé pour cet environnement
        if (self::$config['environments'][$env]['force_disable'] ?? false) {
            return false;
        }

        // Vérifier la configuration globale
        if (!(self::$config['global']['enabled'] ?? true)) {
            return false;
        }

        // Vérifier l'extension requise
        $requiredExtension = self::$config['global']['extension_required'] ?? 'apcu';
        if (!extension_loaded($requiredExtension)) {
            return false;
        }

        // Vérifier la configuration spécifique de la classe
        return self::$config['classes'][$className]['enabled'] ?? true;
    }

    /**
     * Récupère le TTL (Time To Live) pour une classe donnée
     * 
     * @param string $className Nom de la classe
     * @return int TTL en secondes
     */
    public static function getTtl(string $className): int
    {
        self::loadConfig();
        
        return self::$config['classes'][$className]['ttl'] ?? 
               self::$config['global']['default_ttl'] ?? 300;
    }

    /**
     * Récupère le préfixe de cache pour une classe donnée
     * 
     * @param string $className Nom de la classe
     * @return string Préfixe pour les clés de cache
     */
    public static function getPrefix(string $className): string
    {
        self::loadConfig();
        
        return self::$config['classes'][$className]['prefix'] ?? 
               strtolower($className) . '_search_';
    }

    /**
     * Récupère la description du cache pour une classe donnée
     * 
     * @param string $className Nom de la classe
     * @return string Description du cache
     */
    public static function getDescription(string $className): string
    {
        self::loadConfig();
        
        return self::$config['classes'][$className]['description'] ?? 
               "Cache pour les recherches de $className";
    }

    /**
     * Récupère la configuration complète pour une classe
     * 
     * @param string $className Nom de la classe
     * @return array Configuration complète
     */
    public static function getClassConfig(string $className): array
    {
        return [
            'enabled' => self::isEnabled($className),
            'ttl' => self::getTtl($className),
            'prefix' => self::getPrefix($className),
            'description' => self::getDescription($className)
        ];
    }

    /**
     * Vérifie si le mode debug est activé
     * 
     * @return bool True si le mode debug est activé
     */
    public static function isDebugMode(): bool
    {
        self::loadConfig();
        $env = self::getEnvironment();
        
        return self::$config['environments'][$env]['debug_mode'] ?? false;
    }

    /**
     * Récupère les classes qui doivent être invalidées quand une classe change
     * 
     * @param string $className Nom de la classe qui change
     * @return array Liste des classes à invalider
     */
    public static function getInvalidationTargets(string $className): array
    {
        self::loadConfig();
        
        if (!(self::$config['invalidation']['auto_invalidate_related'] ?? true)) {
            return [];
        }
        
        return self::$config['invalidation']['invalidation_patterns'][$className] ?? [];
    }

    /**
     * Invalide tous les caches pour les classes spécifiées
     * 
     * @param array $classNames Liste des noms de classes
     * @return bool True si l'invalidation a réussi
     */
    public static function invalidateClasses(array $classNames): bool
    {
        if (!extension_loaded('apcu')) {
            return false;
        }

        $success = true;
        foreach ($classNames as $className) {
            $prefix = self::getPrefix($className);
            
            // Récupérer toutes les clés de cache
            $cacheInfo = apcu_cache_info();
            if (!isset($cacheInfo['cache_list'])) {
                continue;
            }

            // Invalider toutes les clés qui commencent par le préfixe
            foreach ($cacheInfo['cache_list'] as $entry) {
                if (!isset($entry['info'])) continue;
                $key = $entry['info'];
                
                if (str_starts_with($key, $prefix)) {
                    if (!apcu_delete($key)) {
                        $success = false;
                        if (self::isDebugMode()) {
                            error_log("Échec de l'invalidation du cache: $key");
                        }
                    }
                }
            }
        }
        
        return $success;
    }

    /**
     * Invalide automatiquement les caches liés quand une classe change
     * 
     * @param string $className Nom de la classe qui a changé
     * @return bool True si l'invalidation a réussi
     */
    public static function autoInvalidate(string $className): bool
    {
        $targets = self::getInvalidationTargets($className);
        $targets[] = $className; // Inclure la classe elle-même
        
        return self::invalidateClasses($targets);
    }

    /**
     * Réinitialise la configuration (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$config = null;
        self::$environment = null;
    }

    /**
     * Définit manuellement l'environnement (utile pour les tests)
     * 
     * @param string $environment L'environnement à définir
     */
    public static function setEnvironment(string $environment): void
    {
        self::$environment = $environment;
    }

    /**
     * Récupère toutes les configurations disponibles
     * 
     * @return array Configuration complète
     */
    public static function getAllConfig(): array
    {
        self::loadConfig();
        return self::$config;
    }

    /**
     * Log les informations de cache si le mode debug est activé
     * 
     * @param string $message Message à logger
     * @param array $context Contexte additionnel
     */
    public static function debugLog(string $message, array $context = []): void
    {
        if (self::isDebugMode()) {
            $contextStr = !empty($context) ? ' - Context: ' . json_encode($context) : '';
            error_log("[CacheConfig Debug] $message$contextStr");
        }
    }
}
