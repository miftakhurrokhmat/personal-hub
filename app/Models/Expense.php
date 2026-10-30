<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'spent_at' => 'date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}