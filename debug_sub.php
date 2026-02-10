<?php
// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$traderEmail = 'trader_trail@gmail.com';
$analystEmail = 'yosephtrader@email.com';

echo "Debugging Subscription for:\n";
echo "Trader: $traderEmail\n";
echo "Analyst: $analystEmail\n\n";

$trader = App\Models\User::where('email', $traderEmail)->first();
$analyst = App\Models\User::where('email', $analystEmail)->first();

if (!$trader) { echo "ERROR: Trader not found.\n"; } else { echo "Trader ID: " . $trader->id . "\n"; }
if (!$analyst) { echo "ERROR: Analyst not found.\n"; } else { echo "Analyst ID: " . $analyst->id . "\n"; }

if ($trader && $analyst) {
    $subs = App\Models\Subscription::where('trader_id', $trader->id)
        ->where('analyst_id', $analyst->id)
        ->get();
    
    echo "\nFound " . $subs->count() . " subscription records:\n";
    foreach ($subs as $sub) {
        echo "ID: $sub->id | Status: $sub->status | Plan: $sub->plan | Created: $sub->created_at | TX Ref: $sub->chapa_tx_ref\n";
    }

    if ($subs->isEmpty()) {
        echo "No subscription record found between these two users.\n";
        
        // Check if there are ANY subscriptions for this trader
        $allSubs = App\Models\Subscription::where('trader_id', $trader->id)->get();
        echo "\nTrader has " . $allSubs->count() . " total subscriptions.\n";
    }
}
