<?php

namespace App\Filament\admin\Resources\OTAttendantResource\Pages;

use App\Filament\admin\Resources\OTAttendantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOTAttendant extends EditRecord
{
    protected static string $resource = OTAttendantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
