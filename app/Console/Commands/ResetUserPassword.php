<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password}';
    protected $description = 'Reset a user password';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found: $email");
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password reset for: {$user->name} ({$user->email})");
        return 0;
    }
}
