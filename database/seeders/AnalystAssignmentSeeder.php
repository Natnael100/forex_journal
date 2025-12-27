<?php

namespace Database\Seeders;

use App\Models\AnalystAssignment;
use App\Models\AnalystRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnalystAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $analysts = User::role('analyst')->get();
        $traders = User::role('trader')->get();

        if ($analysts->isEmpty() || $traders->isEmpty()) {
            return;
        }

        // assign 2-3 traders to each analyst
        foreach ($analysts as $index => $analyst) {
            // Pick a subset of traders to assign
            // Simple logic: distribute assigning iteratively
            $tradersToAssign = $traders->slice($index * 2, 2);

            foreach ($tradersToAssign as $trader) {
                // 1. Create the Assignment Record
                AnalystAssignment::firstOrCreate(
                    [
                        'analyst_id' => $analyst->id,
                        'trader_id' => $trader->id,
                    ],
                    [
                        'assigned_by' => User::role('admin')->first()->id ?? 1,
                        'status' => 'active',
                        'start_date' => now()->subMonths(1),
                    ]
                );

                // 2. Create the completed Request Record (to keep history consistent)
                AnalystRequest::firstOrCreate(
                    [
                        'trader_id' => $trader->id,
                        'analyst_id' => $analyst->id,
                    ],
                    [
                        'status' => 'completed', // Completed means assignment finalized
                        'motivation' => 'I verified my PnL and want to scale up.',
                        'admin_notes' => 'Approved by Admin.',
                        'reviewed_by' => User::role('admin')->first()->id ?? 1,
                        'reviewed_at' => now()->subMonths(1),
                        'consented_at' => now()->subMonths(1),
                    ]
                );
            }
        }

        $this->command->info('Analyst Assignments seeded.');
    }
}
