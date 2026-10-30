<?php

namespace App\Filament\Resources\MeetingMinuteResource\Pages;

use App\Filament\Resources\MeetingMinuteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeetingMinute extends EditRecord
{
    protected static string $resource = MeetingMinuteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
