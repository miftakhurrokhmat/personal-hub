<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    //
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('order_column', 'asc');
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
