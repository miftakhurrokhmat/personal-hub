<?php

namespace App\Filament\Resources\ConceptPlanResource\Pages;

use App\Filament\Resources\ConceptPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConceptPlan extends EditRecord
{
    protected static string $resource = ConceptPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
