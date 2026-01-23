<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'trader_id',
        'analyst_id',
        'subscription_id',
        'reason',
        'description',
        'status', // pending, resolved, dismissed
        'admin_notes',
        'resolution', // refund, warning, none
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function trader()
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
