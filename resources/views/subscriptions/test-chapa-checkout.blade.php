<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapa Payment Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full border border-green-500">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600 mb-2">Chapa Simulator</h1>
            <p class="text-sm text-gray-500 uppercase tracking-widest">Test Mode Environment</p>
        </div>

        <div class="space-y-4 mb-8">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Merchant</span>
                <span class="font-bold">Forex Journal App</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Amount</span>
                <span class="font-bold text-xl">{{ number_format($amount, 2) }} ETB</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600">Ref</span>
                <span class="font-mono text-xs">{{ $txRef }}</span>
            </div>
        </div>

        <form action="{{ route('test.chapa.pay') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="tx_ref" value="{{ $txRef }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="meta" value="{{ $meta }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Simulate Payment Method</label>
                <select class="w-full border-gray-300 rounded-md shadow-sm p-2 border">
                    <option>Telebirr</option>
                    <option>CBE Birr</option>
                    <option>Amole</option>
                    <option>Visa/Mastercard</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow transition-colors flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Confirm Payment (Simulation)
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">This is a local simulation. No real money is moved.</p>
        </div>
    </div>
</body>
</html>
