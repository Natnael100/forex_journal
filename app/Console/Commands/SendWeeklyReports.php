<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\WeeklyPerformanceReport;
use App\Services\TradeAnalyticsService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly performance summary to traders';

    /**
     * Execute the console command.
     */
    public function handle(TradeAnalyticsService $analyticsService)
    {
        $this->info('Starting Weekly Report Generation...');

        // Get traders who have active trades in the last week
        // or just all active traders (Phase 1: All active traders)
        $traders = User::role('trader')->where('status', 'active')->get();

        $weekStart = Carbon::now()->startOfWeek()->subWeek(); // Last week
        $weekEnd = Carbon::now()->endOfWeek()->subWeek();

        $bar = $this->output->createProgressBar(count($traders));
        $bar->start();

        foreach ($traders as $trader) {
            // Check if trader has trades in the last week
            $tradeCount = $trader->trades()
                ->whereBetween('entry_date', [$weekStart, $weekEnd])
                ->count();

            if ($tradeCount > 0) {
                // Calculate Stats
                // Filter for "Last Week"
                $filters = ['date_range' => 'last_week']; 
                // Note: TradeAnalyticsService might need specific date range args if 'last_week' string isn't built-in
                // Assuming we can pass dates directly or use a helper
                
                // Let's manually calculate for robustness here or use service methods
                $trades = $trader->trades()
                    ->whereBetween('entry_date', [$weekStart, $weekEnd])
                    ->get();

                $winRate = $analyticsService->calculateWinRate($trades);
                $totalPL = $trades->sum('profit_loss');
                $bestPair = $this->getBestPair($trades);

                $stats = [
                    'week_start' => $weekStart->format('M d'),
                    'week_end' => $weekEnd->format('M d'),
                    'trade_count' => $tradeCount,
                    'win_rate' => $winRate,
                    'profit_loss' => $totalPL,
                    'best_pair' => $bestPair
                ];

                // Send Email
                Mail::to($trader->email)->queue(new WeeklyPerformanceReport($trader, $stats));
                
                $this->info(" Report queued for {$trader->name}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Weekly Reports Sent Successfully!');
    }

    private function getBestPair($trades)
    {
        if ($trades->isEmpty()) return 'N/A';
        
        return $trades->groupBy('pair')
            ->sortByDesc(function ($group) {
                return $group->sum('profit_loss');
            })
            ->keys()
            ->first();
    }
}
