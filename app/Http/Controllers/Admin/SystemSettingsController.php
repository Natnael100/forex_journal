<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings
     */
    public function index()
    {
        // Get all settings (or use defaults)
        $settings = $this->getSettings();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Notification Settings
            'notifications_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'feedback_notifications' => 'boolean',
            'assignment_notifications' => 'boolean',
            
            // AI Analysis Thresholds
            'min_win_rate' => 'nullable|numeric|min:0|max:100',
            'min_risk_reward' => 'nullable|numeric|min:0',
            'max_drawdown' => 'nullable|numeric|min:0|max:100',
            'min_trades_for_analysis' => 'nullable|integer|min:1',
            
            // System
            'platform_name' => 'nullable|string|max:255',
            'items_per_page' => 'nullable|integer|min:5|max:100',
        ]);

        // Save settings
        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value ?? '');
        }

        activity()
            ->causedBy(auth()->user())
            ->log('Updated system settings');

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Get all settings with defaults
     */
    private function getSettings()
    {
        $defaults = [
            // Notifications
            'notifications_enabled' => true,
            'email_notifications' => true,
            'feedback_notifications' => true,
            'assignment_notifications' => true,
            
            // AI Thresholds
            'min_win_rate' => 45,
            'min_risk_reward' => 1.5,
            'max_drawdown' => 20,
            'min_trades_for_analysis' => 20,
            
            // System
            'platform_name' => 'Forex Trading Journal',
            'items_per_page' => 20,
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = $this->getSetting($key, $default);
        }

        return $settings;
    }

    /**
     * Get a specific setting
     */
    private function getSetting($key, $default = null)
    {
        try {
            $setting = DB::table('settings')->where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set a specific setting
     */
    private function setSetting($key, $value)
    {
        try {
            // Ensure settings table exists
            if (!DB::getSchemaBuilder()->hasTable('settings')) {
                DB::statement('CREATE TABLE IF NOT EXISTS settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    key TEXT NOT NULL UNIQUE,
                    value TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )');
            }

            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            \Log::error("Failed to set setting {$key}: " . $e->getMessage());
        }
    }
}
