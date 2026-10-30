<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppAccount extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'port' => 'integer',
        'password' => 'encrypted',
        'secret_key_or_token' => 'encrypted',
    ];

    // Relasi ke Client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Relasi ke Project
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}