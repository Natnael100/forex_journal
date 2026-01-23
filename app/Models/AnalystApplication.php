<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalystApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'country',
        'timezone',
        'phone',
        'years_experience',
        'certifications',
        'certificate_files',
        'methodology',
        'specializations',
        'coaching_experience',
        'clients_coached',
        'coaching_style',
        'track_record_url',
        'linkedin_url',
        'twitter_handle',
        'youtube_url',
        'website_url',
        'why_join',
        'unique_value',
        'max_clients',
        'communication_methods',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'certifications' => 'array',
        'certificate_files' => 'array',
        'methodology' => 'array',
        'specializations' => 'array',
        'communication_methods' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Admin who reviewed the application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * User account created from this application
     */
    public function user()
    {
        return $this->hasOne(User::class, 'application_id');
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
