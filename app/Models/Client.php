<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $guarded = ['id'];

    // Relasi ke City (Kota/Kabupaten)
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(AppAccount::class);
    }
}