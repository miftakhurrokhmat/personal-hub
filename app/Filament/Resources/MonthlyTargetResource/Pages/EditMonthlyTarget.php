<?php

namespace App\Filament\Resources\MonthlyTargetResource\Pages;

use App\Filament\Resources\MonthlyTargetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyTarget extends EditRecord
{
    protected static string $resource = MonthlyTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
