<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting fix...\n";
file_put_contents('fix_log.txt', "Starting fix...\n");

try {
    // 1. Strategies Table
    if (!Schema::hasTable('strategies')) {
        file_put_contents('fix_log.txt', "Creating strategies table...\n", FILE_APPEND);
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        file_put_contents('fix_log.txt', "strategies table created.\n", FILE_APPEND);
    } else {
        file_put_contents('fix_log.txt', "strategies table already exists.\n", FILE_APPEND);
    }

    // 2. Trades Table Columns
    Schema::table('trades', function (Blueprint $table) {
        $columns = [
            'strategy_id' => fn() => $table->foreignId('strategy_id')->nullable()->constrained('strategies')->nullOnDelete(),
            'trade_type' => fn() => $table->string('trade_type')->nullable(),
            'entry_price' => fn() => $table->decimal('entry_price', 16, 8)->nullable(),
            'exit_price' => fn() => $table->decimal('exit_price', 16, 8)->nullable(),
            'stop_loss' => fn() => $table->decimal('stop_loss', 16, 8)->nullable(),
            'take_profit' => fn() => $table->decimal('take_profit', 16, 8)->nullable(),
            'lot_size' => fn() => $table->decimal('lot_size', 8, 2)->nullable(),
            'risk_percentage' => fn() => $table->decimal('risk_percentage', 5, 2)->nullable(),
            'pre_trade_emotion' => fn() => $table->string('pre_trade_emotion')->nullable(),
            'post_trade_emotion' => fn() => $table->string('post_trade_emotion')->nullable(),
            'followed_plan' => fn() => $table->boolean('followed_plan')->nullable(),
            'mistakes_lessons' => fn() => $table->text('mistakes_lessons')->nullable(),
            'setup_notes' => fn() => $table->text('setup_notes')->nullable(),
            'chart_link' => fn() => $table->string('chart_link')->nullable(),
        ];

        foreach ($columns as $name => $def) {
            if (!Schema::hasColumn('trades', $name)) {
                file_put_contents('fix_log.txt', "Adding column $name...\n", FILE_APPEND);
                $def();
            }
        }
    });

    file_put_contents('fix_log.txt', "Fix Complete.\n", FILE_APPEND);

} catch (\Exception $e) {
    file_put_contents('fix_log.txt', "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString(), FILE_APPEND);
}
