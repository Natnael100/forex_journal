<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'trader_id',
    ];

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function trader()
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
