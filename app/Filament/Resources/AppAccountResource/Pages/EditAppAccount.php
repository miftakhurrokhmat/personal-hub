<?php

namespace App\Filament\Resources\AppAccountResource\Pages;

use App\Filament\Resources\AppAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppAccount extends EditRecord
{
    protected static string $resource = AppAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
