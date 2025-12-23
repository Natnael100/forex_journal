@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-8 text-sm text-slate-400">
        <a href="{{ route('trader.accounts.index') }}" class="hover:text-white transition-colors">Accounts</a>
        <span class="mx-2">/</span>
        <span class="text-white">New Account</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Create New Account</h1>
        <p class="text-slate-400">Set up a new trading account to track your performance separately.</p>
    </div>

    <!-- Form -->
    <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-8 border border-slate-700/50 shadow-xl">
        <form action="{{ route('trader.accounts.store') }}" method="POST">
            @csrf

            <!-- Account Name -->
            <div class="mb-6">
                <label for="account_name" class="block text-sm font-medium text-slate-300 mb-2">Account Name</label>
                <input type="text" name="account_name" id="account_name" 
                    class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-600"
                    placeholder="e.g., My Funded Challenge, Personal Account"
                    value="{{ old('account_name') }}" required>
                @error('account_name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Account Type</label>
                    <div class="space-y-3">
                        @foreach($accountTypes as $type)
                        <label class="flex items-center p-3 rounded-lg border border-slate-700 cursor-pointer hover:bg-slate-700/50 transition-colors {{ old('account_type') == $type->value ? 'bg-blue-500/10 border-blue-500' : 'bg-slate-900/30' }}">
                            <input type="radio" name="account_type" value="{{ $type->value }}" 
                                class="w-4 h-4 text-blue-500 bg-slate-800 border-slate-600 focus:ring-blue-500 focus:ring-offset-slate-900"
                                {{ old('account_type') == $type->value ? 'checked' : '' }} required>
                            <div class="ml-3 flex items-center gap-3">
                                <span class="text-xl">{{ $type->icon() }}</span>
                                <span class="text-white font-medium">{{ $type->label() }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('account_type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-6">
                    <!-- Broker -->
                    <div>
                        <label for="broker" class="block text-sm font-medium text-slate-300 mb-2">Broker (Optional)</label>
                        <input type="text" name="broker" id="broker" 
                            class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-600"
                            placeholder="e.g., FTMO, IC Markets"
                            value="{{ old('broker') }}">
                    </div>

                    <!-- Initial Balance -->
                    <div>
                        <label for="initial_balance" class="block text-sm font-medium text-slate-300 mb-2">Initial Balance</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                            <input type="number" step="0.01" name="initial_balance" id="initial_balance" 
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-lg pl-8 pr-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-600"
                                placeholder="10000.00"
                                value="{{ old('initial_balance') }}" required>
                        </div>
                        @error('initial_balance')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-slate-300 mb-2">Currency</label>
                        <select name="currency" id="currency" 
                            class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="AUD">AUD - Australian Dollar</option>
                            <option value="JPY">JPY - Japanese Yen</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-700/50">
                <a href="{{ route('trader.accounts.index') }}" class="px-6 py-2.5 text-slate-300 hover:text-white font-medium transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-900/20 transition-all transform hover:scale-105">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
