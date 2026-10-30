<?php

namespace App\Filament\Resources\ExpenseReceiptResource\Pages;

use App\Filament\Resources\ExpenseReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenseReceipt extends EditRecord
{
    protected static string $resource = ExpenseReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
