<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalystPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'tier',
        'price',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }
}
