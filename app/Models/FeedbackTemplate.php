<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'category',
        'title',
        'content',
    ];

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }
}
