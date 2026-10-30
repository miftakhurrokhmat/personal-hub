<?php

namespace App\Filament\Resources\AppAccountResource\Pages;

use App\Filament\Resources\AppAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppAccounts extends ListRecords
{
    protected static string $resource = AppAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
