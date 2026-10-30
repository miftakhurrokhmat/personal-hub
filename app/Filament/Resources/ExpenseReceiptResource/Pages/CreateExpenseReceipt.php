<?php

namespace App\Filament\Resources\ExpenseReceiptResource\Pages;

use App\Filament\Resources\ExpenseReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseReceipt extends CreateRecord
{
    protected static string $resource = ExpenseReceiptResource::class;
}
