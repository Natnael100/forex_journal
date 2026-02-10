<?php

namespace App\Imports;

use App\Models\Trade;
use App\Enums\MarketSession;
use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TradesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip empty rows
        if (!isset($row['pair']) || !isset($row['entry_price'])) {
            return null;
        }

        // Calculate Pips/PL if needed or use imported values
        $entry = $row['entry_price'];
        $exit = $row['exit_price'];
        $pair = strtoupper($row['pair']);
        
        // Normalize direction
        $rawDirection = trim($row['direction']);
        $direction = strtolower($rawDirection);
        if ($direction === 'long') $direction = 'buy';
        if ($direction === 'short') $direction = 'sell';
        
        $pips = $this->calculatePips($entry, $exit, $direction, $pair);
        $pl = $row['profit_loss'] ?? ($pips * ($row['lot_size'] ?? 0.01) * 10); // Approximation if missing

        return new Trade([
            'user_id' => Auth::id(),
            'trade_account_id' => $this->getAccountId($row['account_name'] ?? 'Default'),
            'pair' => $pair,
            'trade_type' => $row['type'] ?? 'Standard',
            'direction' => $direction,
            'session' => $this->mapSession($row['session'] ?? null, $row['entry_date']),
            'entry_price' => $entry,
            'exit_price' => $exit,
            'lot_size' => $row['lot_size'],
            'stop_loss' => $row['stop_loss'] ?? null,
            'take_profit' => $row['take_profit'] ?? null,
            'risk_reward_ratio' => $row['risk_reward'] ?? $this->calculateRR($entry, $row['stop_loss'] ?? null, $row['take_profit'] ?? null),
            'pips' => $pips,
            'profit_loss' => $pl,
            'outcome' => $pl > 0 ? TradeOutcome::WIN->value : ($pl < 0 ? TradeOutcome::LOSS->value : TradeOutcome::BREAKEVEN->value),
            'entry_date' => $this->parseDate($row['entry_date']),
            'exit_date' => isset($row['exit_date']) ? $this->parseDate($row['exit_date']) : null,
            'notes' => $row['notes'] ?? null,
            'strategy_id' => null, // Strategy mapping is complex, leaving null for MVP
        ]);
    }

    public function rules(): array
    {
        return [
            'pair' => 'required|string',
            'direction' => 'required|in:buy,sell,BUY,SELL,long,short,LONG,SHORT,Long,Short',
            'entry_date' => 'required',
            'entry_price' => 'required|numeric',
            'exit_price' => 'required|numeric',
            'lot_size' => 'required|numeric',
        ];
    }

    private function calculatePips($entry, $exit, $direction, $pair)
    {
        $multiplier = str_contains($pair, 'JPY') ? 100 : 10000;
        $diff = ($direction === 'buy') ? ($exit - $entry) : ($entry - $exit);
        return round($diff * $multiplier, 2);
    }

    private function calculateRR($entry, $sl, $tp)
    {
        if (!$sl || !$tp) return 0;
        $risk = abs($entry - $sl);
        $reward = abs($entry - $tp);
        return $risk > 0 ? round($reward / $risk, 2) : 0;
    }

    private function mapSession($sessionName, $date)
    {
        if ($sessionName) {
            $sessionName = strtoupper($sessionName);
            foreach (MarketSession::cases() as $s) {
                if (str_contains($sessionName, strtoupper($s->value))) {
                    return $s->value;
                }
            }
        }
        
        // Fallback to time-based
        $hour = Carbon::parse($date)->hour;
        if ($hour >= 8 && $hour < 16) return MarketSession::LONDON->value;
        if ($hour >= 13 && $hour < 21) return MarketSession::NEWYORK->value;
        if ($hour >= 21 || $hour < 5) return MarketSession::SYDNEY->value;
        return MarketSession::ASIA->value;
    }

    private function getAccountId($name)
    {
        // Simple logic: get first account or create if needed
        // For MVP, just return the first account of the user
        $account = Auth::user()->tradeAccounts()->first();
        return $account ? $account->id : null;
    }

    private function parseDate($date)
    {
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return now();
        }
    }
}
