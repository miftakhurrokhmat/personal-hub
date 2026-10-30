<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseReceipt extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['receipt_date' => 'date', 'amount' => 'decimal:2'];

    public function expense(): BelongsTo { return $this->belongsTo(Expense::class); }
}