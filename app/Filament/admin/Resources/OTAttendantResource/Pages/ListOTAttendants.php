<?php

namespace App\Filament\admin\Resources\OTAttendantResource\Pages;

use App\Filament\admin\Resources\OTAttendantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOTAttendants extends ListRecords
{
    protected static string $resource = OTAttendantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
