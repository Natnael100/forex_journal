<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalystRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'trader_id',
        'analyst_id',
        'status',
        'motivation',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'consented_at',
        'ip_address',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'consented_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed'; // Admin approved, waiting for analyst? Or just 'approved'
    const STATUS_APPROVED = 'approved'; // Admin approved, waiting for consent
    const STATUS_REJECTED = 'rejected';
    const STATUS_CONSENTED = 'consented'; // Trader aggreed, ready to assign
    const STATUS_COMPLETED = 'completed'; // Assignment created

    // Relationships
    public function trader()
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
