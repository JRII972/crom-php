<?php

declare(strict_types=1);

namespace App\Database\Traits;

use App\Utils\CacheConfig;

/**
 * Trait pour gérer les fonctionnalités de cache de manière centralisée
 * 
 * Ce trait fournit les méthodes communes pour la gestion du cache
 * dans toutes les classes App\Database\Types
 */
trait CacheableTrait
{
    /**
     * Récupère la configuration de cache pour cette classe
     * 
     * @return array Configuration du cache
     */
    protected static function getCacheConfig(): array
    {
        $className = static::getClassName();
        return CacheConfig::getClassConfig($className);
    }

    /**
     * Récupère le nom de la classe sans namespace
     * 
     * @return string Nom de la classe
     */
    protected static function getClassName(): string
    {
        $fullClassName = static::class;
        $parts = explode('\\', $fullClassName);
        return end($parts);
    }

    /**
     * Vérifie si le cache est activé pour cette classe
     * 
     * @return bool True si le cache est activé
     */
    protected static function isCacheEnabled(): bool
    {
        return CacheConfig::isEnabled(static::getClassName());
    }

    /**
     * Récupère le TTL du cache pour cette classe
     * 
     * @return int TTL en secondes
     */
    protected static function getCacheTtl(): int
    {
        return CacheConfig::getTtl(static::getClassName());
    }

    /**
     * Récupère le préfixe de cache pour cette classe
     * 
     * @return string Préfixe des clés de cache
     */
    protected static function getCachePrefix(): string
    {
        return CacheConfig::getPrefix(static::getClassName());
    }

    /**
     * Génère une clé de cache unique basée sur les paramètres
     * 
     * @param array $params Paramètres pour générer la clé
     * @return string Clé de cache unique
     */
    protected static function generateCacheKey(array $params): string
    {
        $prefix = static::getCachePrefix();
        $hash = md5(serialize($params));
        return $prefix . $hash;
    }

    /**
     * Récupère une valeur depuis le cache
     * 
     * @param string $key Clé de cache
     * @return mixed|false Valeur du cache ou false si non trouvée
     */
    protected static function getCacheValue(string $key)
    {
        if (!static::isCacheEnabled() || !extension_loaded('apcu')) {
            return false;
        }

        CacheConfig::debugLog("Cache GET attempt", ['key' => $key, 'class' => static::getClassName()]);
        
        $result = apcu_fetch($key);
        
        if ($result !== false) {
            CacheConfig::debugLog("Cache HIT", ['key' => $key, 'class' => static::getClassName()]);
        } else {
            CacheConfig::debugLog("Cache MISS", ['key' => $key, 'class' => static::getClassName()]);
        }
        
        return $result;
    }

    /**
     * Stocke une valeur dans le cache
     * 
     * @param string $key Clé de cache
     * @param mixed $value Valeur à stocker
     * @return bool True si le stockage a réussi
     */
    protected static function setCacheValue(string $key, $value): bool
    {
        if (!static::isCacheEnabled() || !extension_loaded('apcu')) {
            return false;
        }

        $ttl = static::getCacheTtl();
        $result = apcu_store($key, $value, $ttl);
        
        CacheConfig::debugLog("Cache SET", [
            'key' => $key, 
            'class' => static::getClassName(),
            'ttl' => $ttl,
            'success' => $result
        ]);
        
        return $result;
    }

    /**
     * Invalide le cache pour cette classe
     * 
     * @return bool True si l'invalidation a réussi
     */
    protected static function invalidateCache(): bool
    {
        if (!extension_loaded('apcu')) {
            return false;
        }

        CacheConfig::debugLog("Cache invalidation started", ['class' => static::getClassName()]);
        
        $success = CacheConfig::autoInvalidate(static::getClassName());
        
        CacheConfig::debugLog("Cache invalidation completed", [
            'class' => static::getClassName(),
            'success' => $success
        ]);
        
        return $success;
    }

    /**
     * Invalide une clé de cache spécifique
     * 
     * @param string $key Clé de cache à invalider
     * @return bool True si l'invalidation a réussi
     */
    protected static function deleteCacheKey(string $key): bool
    {
        if (!extension_loaded('apcu')) {
            return false;
        }

        $result = apcu_delete($key);
        
        CacheConfig::debugLog("Cache DELETE", [
            'key' => $key,
            'class' => static::getClassName(),
            'success' => $result
        ]);
        
        return $result;
    }

    /**
     * Exécute une fonction avec cache automatique
     * 
     * @param callable $dataProvider Fonction qui fournit les données si pas en cache
     * @param array $cacheParams Paramètres pour générer la clé de cache
     * @return mixed Résultat (depuis le cache ou calculé)
     */
    protected static function withCache(callable $dataProvider, array $cacheParams)
    {
        $cacheKey = static::generateCacheKey($cacheParams);
        
        // Essayer de récupérer depuis le cache
        $cachedResult = static::getCacheValue($cacheKey);
        if ($cachedResult !== false) {
            return $cachedResult;
        }
        
        // Calculer les données
        $result = $dataProvider();
        
        // Stocker en cache
        static::setCacheValue($cacheKey, $result);
        
        return $result;
    }

    /**
     * Récupère les statistiques de cache pour cette classe
     * 
     * @return array Statistiques du cache
     */
    protected static function getCacheStats(): array
    {
        if (!extension_loaded('apcu')) {
            return ['error' => 'Extension APCu non disponible'];
        }

        $prefix = static::getCachePrefix();
        $cacheInfo = apcu_cache_info();
        
        $stats = [
            'class' => static::getClassName(),
            'prefix' => $prefix,
            'enabled' => static::isCacheEnabled(),
            'ttl' => static::getCacheTtl(),
            'keys_count' => 0,
            'total_size' => 0,
            'keys' => []
        ];

        if (isset($cacheInfo['cache_list'])) {
            foreach ($cacheInfo['cache_list'] as $entry) {
                if (!isset($entry['info'])) continue;
                $key = $entry['info'];
                
                if (str_starts_with($key, $prefix)) {
                    $stats['keys_count']++;
                    $stats['total_size'] += $entry['mem_size'] ?? 0;
                    $stats['keys'][] = [
                        'key' => $key,
                        'size' => $entry['mem_size'] ?? 0,
                        'ttl' => $entry['ttl'] ?? 0,
                        'hits' => $entry['num_hits'] ?? 0
                    ];
                }
            }
        }

        return $stats;
    }
}
