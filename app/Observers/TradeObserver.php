<?php

namespace App\Observers;

use App\Models\Trade;
use App\Services\AnalysisCacheService;
use Illuminate\Support\Facades\Log;

/**
 * TradeObserver
 * 
 * Handles cache invalidation when trades are modified.
 * Ensures analysis cache stays synchronized with trade changes.
 */
class TradeObserver
{
    protected $cacheService;

    public function __construct(AnalysisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Trade "created" event.
     */
    public function created(Trade $trade): void
    {
        $this->invalidateTraderCache($trade);
    }

    /**
     * Handle the Trade "updated" event.
     */
    public function updated(Trade $trade): void
    {
        $this->invalidateTraderCache($trade);
    }

    /**
     * Handle the Trade "deleted" event.
     */
    public function deleted(Trade $trade): void
    {
        $this->invalidateTraderCache($trade);
    }

    /**
     * Invalidate all cached analysis for this trader
     */
    protected function invalidateTraderCache(Trade $trade): void
    {
        if ($trade->user_id) {
            $this->cacheService->invalidateTraderCache($trade->user_id);
            
            Log::info("Trade cache invalidated for trader {$trade->user_id}", [
                'trade_id' => $trade->id,
                'event' => debug_backtrace()[1]['function'] ?? 'unknown',
            ]);
        }
    }
}
