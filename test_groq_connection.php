<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

$apiKey = env('GROQ_API_KEY');
$baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
$model = 'llama3-70b-8192';

echo "Testing Groq API Connection...\n";
echo "API Key check: " . ($apiKey ? "Found" : "MISSING") . "\n";
echo "URL: $baseUrl\n";
echo "Model: $model\n";

try {
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $apiKey,
    ])->post($baseUrl, [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system', // Fixed: 'api' is incorrect
                'content' => 'You are a helpful assistant. return JSON: {"status": "ok"}'
            ],
            [
                'role' => 'user',
                'content' => 'Test connection'
            ]
        ],
        'response_format' => ['type' => 'json_object'],
        'temperature' => 0.7,
        'max_tokens' => 1000,
    ]);

    echo "Status Code: " . $response->status() . "\n";
    
    if ($response->successful()) {
        echo "Response: " . $response->body() . "\n";
        echo "SUCCESS!\n";
    } else {
        echo "FAILED.\n";
        echo "Body: " . $response->body() . "\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
