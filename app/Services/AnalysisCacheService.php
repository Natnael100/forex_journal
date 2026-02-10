<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AnalysisCacheService
 * 
 * Manages event-based caching for trader performance analysis.
 * Cache is invalidated when trades are created/updated/deleted.
 */
class AnalysisCacheService
{
    protected const CACHE_TTL = 3600; // 1 hour in seconds
    protected const CACHE_PREFIX = 'analysis';

    /**
     * Get cached analysis if available
     */
    public function getCachedAnalysis(int $traderId, array $filters = []): ?array
    {
        $key = $this->generateCacheKey($traderId, $filters);
        
        if (Cache::has($key)) {
            Log::info("Cache HIT for trader {$traderId}", ['key' => $key]);
            return Cache::get($key);
        }

        Log::info("Cache MISS for trader {$traderId}", ['key' => $key]);
        return null;
    }

    /**
     * Cache analysis results
     */
    public function cacheAnalysis(int $traderId, array $filters, array $analysis): void
    {
        $key = $this->generateCacheKey($traderId, $filters);
        
        Cache::put($key, $analysis, now()->addSeconds(self::CACHE_TTL));
        
        Log::info("Cached analysis for trader {$traderId}", [
            'key' => $key,
            'ttl' => self::CACHE_TTL,
        ]);
    }

    /**
     * Invalidate all cache entries for a specific trader
     */
    public function invalidateTraderCache(int $traderId): void
    {
        // Laravel doesn't support wildcard deletion natively in all cache drivers
        // Store trader-specific keys in a set for efficient invalidation
        $keysListKey = $this->getTraderKeysListKey($traderId);
        $keys = Cache::get($keysListKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($keysListKey);
        
        Log::info("Invalidated all cache for trader {$traderId}", [
            'keys_cleared' => count($keys),
        ]);
    }

    /**
     * Generate cache key from trader ID and filters
     */
    public function generateCacheKey(int $traderId, array $filters): string
    {
        $filterHash = $this->generateFilterHash($filters);
        $key = sprintf('%s_trader_%d_%s', self::CACHE_PREFIX, $traderId, $filterHash);

        // Track this key for the trader (for invalidation)
        $this->trackKeyForTrader($traderId, $key);

        return $key;
    }

    /**
     * Generate deterministic hash from filters
     */
    public function generateFilterHash(array $filters): string
    {
        // Sort keys for deterministic hash
        ksort($filters);
        
        // Remove empty values
        $filters = array_filter($filters, fn($value) => !empty($value));
        
        if (empty($filters)) {
            return 'default';
        }

        return md5(json_encode($filters));
    }

    /**
     * Track cache key for a trader (for bulk invalidation)
     */
    protected function trackKeyForTrader(int $traderId, string $key): void
    {
        $keysListKey = $this->getTraderKeysListKey($traderId);
        $existingKeys = Cache::get($keysListKey, []);

        if (!in_array($key, $existingKeys)) {
            $existingKeys[] = $key;
            Cache::put($keysListKey, $existingKeys, now()->addHours(24));
        }
    }

    /**
     * Get the key used to store the list of cache keys for a trader
     */
    protected function getTraderKeysListKey(int $traderId): string
    {
        return sprintf('%s_trader_%d_keys', self::CACHE_PREFIX, $traderId);
    }

    /**
     * Clear all analysis cache (admin utility)
     */
    public function clearAllCache(): void
    {
        // This is a brute-force approach - use sparingly
        Cache::flush();
        Log::warning('ALL analysis cache cleared (global flush)');
    }

    /**
     * Get cache statistics for a trader
     */
    public function getCacheStats(int $traderId): array
    {
        $keysListKey = $this->getTraderKeysListKey($traderId);
        $keys = Cache::get($keysListKey, []);

        $hitKeys = [];
        $missKeys = [];

        foreach ($keys as $key) {
            if (Cache::has($key)) {
                $hitKeys[] = $key;
            } else {
                $missKeys[] = $key;
            }
        }

        return [
            'trader_id' => $traderId,
            'total_keys' => count($keys),
            'cached_keys' => count($hitKeys),
            'expired_keys' => count($missKeys),
            'hit_rate' => count($keys) > 0 ? round((count($hitKeys) / count($keys)) * 100, 1) : 0,
        ];
    }
}
