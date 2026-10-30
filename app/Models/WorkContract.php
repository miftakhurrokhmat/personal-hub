<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkContract extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'contract_value' => 'decimal:2'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
}