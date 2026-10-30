<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentLetter extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}