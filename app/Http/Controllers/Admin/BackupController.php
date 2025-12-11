<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    protected $backupPath = 'backups';

    /**
     * Display backup dashboard
     */
    public function index()
    {
        // Ensure backup directory exists
        if (!Storage::exists($this->backupPath)) {
            Storage::makeDirectory($this->backupPath);
        }

        // Get all backup files
        $backupFiles = Storage::files($this->backupPath);
        
        $backups = collect($backupFiles)->map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'size' => Storage::size($file),
                'date' => Storage::lastModified($file),
            ];
        })->sortByDesc('date')->values();

        // Database info
        $dbPath = database_path('database.sqlite');
        $dbSize = file_exists($dbPath) ? filesize($dbPath) : 0;

        $stats = [
            'total_backups' => $backups->count(),
            'total_size' => $backups->sum('size'),
            'latest_backup' => $backups->first(),
            'db_size' => $dbSize,
        ];

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    /**
     * Create a new backup
     */
    public function create()
    {
        try {
            $timestamp = now()->format('Y-m-d_His');
            $backupFilename = "backup_{$timestamp}.sqlite";
            $backupPath = storage_path("app/{$this->backupPath}/{$backupFilename}");

            // Get SQLite database path
            $dbPath = database_path('database.sqlite');

            if (!file_exists($dbPath)) {
                return redirect()
                    ->route('admin.backups.index')
                    ->with('error', 'Database file not found!');
            }

            // Ensure backup directory exists
            $backupDir = storage_path("app/{$this->backupPath}");
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Copy the SQLite database file
            if (copy($dbPath, $backupPath)) {
                activity()
                    ->causedBy(auth()->user())
                    ->log("Created database backup: {$backupFilename}");

                return redirect()
                    ->route('admin.backups.index')
                    ->with('success', "Backup created successfully: {$backupFilename}");
            } else {
                return redirect()
                    ->route('admin.backups.index')
                    ->with('error', 'Failed to create backup!');
            }
        } catch (\Exception $e) {
            \Log::error('Backup creation failed: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download($filename)
    {
        $filePath = "{$this->backupPath}/{$filename}";

        if (!Storage::exists($filePath)) {
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Backup file not found!');
        }

        activity()
            ->causedBy(auth()->user())
            ->log("Downloaded backup: {$filename}");

        return Storage::download($filePath, $filename);
    }

    /**
     * Delete a backup file
     */
    public function destroy($filename)
    {
        $filePath = "{$this->backupPath}/{$filename}";

        if (!Storage::exists($filePath)) {
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Backup file not found!');
        }

        Storage::delete($filePath);

        activity()
            ->causedBy(auth()->user())
            ->log("Deleted backup: {$filename}");

        return redirect()
            ->route('admin.backups.index')
            ->with('success', 'Backup deleted successfully!');
    }

    /**
     * Restore from backup file
     */
    public function restore(Request $request, $filename)
    {
        try {
            $backupPath = storage_path("app/{$this->backupPath}/{$filename}");
            $dbPath = database_path('database.sqlite');

            if (!file_exists($backupPath)) {
                return redirect()
                    ->route('admin.backups.index')
                    ->with('error', 'Backup file not found!');
            }

            // Create a backup of current state before restoring
            $preRestoreBackup = "pre-restore_" . now()->format('Y-m-d_His') . ".sqlite";
            $preRestorePath = storage_path("app/{$this->backupPath}/{$preRestoreBackup}");
            copy($dbPath, $preRestorePath);

            // Close all database connections
            DB::disconnect();

            // Restore the backup
            if (copy($backupPath, $dbPath)) {
                // Reconnect to database
                DB::reconnect();

                activity()
                    ->causedBy(auth()->user())
                    ->log("Restored database from backup: {$filename}");

                return redirect()
                    ->route('admin.backups.index')
                    ->with('success', "Database restored successfully from {$filename}! A pre-restore backup was created: {$preRestoreBackup}");
            } else {
                return redirect()
                    ->route('admin.backups.index')
                    ->with('error', 'Failed to restore backup!');
            }
        } catch (\Exception $e) {
            \Log::error('Backup restore failed: ' . $e->getMessage());
            
            return redirect()
                ->route('admin.backups.index')
                ->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}
