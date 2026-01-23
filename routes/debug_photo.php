<?php
// DEBUG ROUTE - Check photo values
Route::get('/debug-photo/{userId}', function($userId) {
    $user = \App\Models\User::find($userId);
    
    if (!$user) {
        return "User not found";
    }
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'username' => $user->username,
        'profile_photo_db' => $user->profile_photo,
        'cover_photo_db' => $user->cover_photo,
        'profile_photo_url' => $user->getProfilePhotoUrl(),
        'cover_photo_url' => $user->getCoverPhotoUrl(),
        'files_in_storage' => \Illuminate\Support\Facades\Storage::disk('public')->files('profiles'),
    ]);
});
