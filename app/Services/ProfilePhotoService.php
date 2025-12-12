<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoService
{
    protected $disk;
    protected $path;

    public function __construct()
    {
        $this->disk = config('profile.storage.disk', 'public');
        $this->path = config('profile.storage.path', 'profiles');
    }

    /**
     * Upload and process profile photo
     */
    public function uploadProfilePhoto(UploadedFile $file, User $user): string
    {
        // Delete old photo if exists
        $this->deletePhoto($user, 'profile');
        
        // Generate unique filename
        $filename = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store the file directly (copy for all sizes - will resize later when GD is enabled)
        $file->storeAs($this->path, 'large_' . $filename, $this->disk);
        copy($file->getPathname(), storage_path('app/public/' . $this->path . '/medium_' . $filename));
        copy($file->getPathname(), storage_path('app/public/' . $this->path . '/small_' . $filename));
        
        // Update user
        $user->update(['profile_photo' => $filename]);
        
        return $filename;
    }

    /**
     * Upload and process cover photo
     */
    public function uploadCoverPhoto(UploadedFile $file, User $user): string
    {
        // Delete old cover if exists
        $this->deletePhoto($user, 'cover');
        
        // Generate unique filename
        $filename = 'cover_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store the file
        $file->storeAs($this->path, $filename, $this->disk);
        
        // Update user
        $user->update(['cover_photo' => $filename]);
        
        return $filename;
    }

    /**
     * Delete photo files
     */
    public function deletePhoto(User $user, string $type): bool
    {
        if ($type === 'profile' && $user->profile_photo) {
            $filename = $user->profile_photo;
            
            // Delete all variants
            foreach (['large', 'medium', 'small'] as $size) {
                $path = $this->path . '/' . $size . '_' . $filename;
                Storage::disk($this->disk)->delete($path);
            }
            
            $user->update(['profile_photo' => null]);
            return true;
        }
        
        if ($type === 'cover' && $user->cover_photo) {
            $path = $this->path . '/' . $user->cover_photo;
            Storage::disk($this->disk)->delete($path);
            
            $user->update(['cover_photo' => null]);
            return true;
        }
        
        return false;
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrl(User $user, string $size = 'large'): string
    {
        if ($user->profile_photo) {
            return asset('storage/' . $this->path . '/' . $size . '_' . $user->profile_photo);
        }
        
        return asset(config('profile.defaults.avatar', 'images/default-avatar.png'));
    }

    /**
     * Get cover URL
     */
    public function getCoverUrl(User $user): string
    {
        if ($user->cover_photo) {
            return asset('storage/' . $this->path . '/' . $user->cover_photo);
        }
        
        return asset(config('profile.defaults.cover', 'images/default-cover.jpg'));
    }

    /**
     * Validate photo file
     */
    public function validatePhoto(UploadedFile $file): bool
    {
        $maxSize = config('profile.photo.max_size', 2048) * 1024;
        
        return $file->isValid() &&
               in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']) &&
               $file->getSize() <= $maxSize;
    }

    /**
     * Validate cover file
     */
    public function validateCover(UploadedFile $file): bool
    {
        $maxSize = config('profile.cover.max_size', 4096) * 1024;
        
        return $file->isValid() &&
               in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']) &&
               $file->getSize() <= $maxSize;
    }
}
