<?php

namespace App\Filament\Receptionist\Resources\AnesthesiologistResource\Pages;

use App\Filament\Receptionist\Resources\AnesthesiologistResource;
use App\Filament\Receptionist\Resources\OTAttendantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnesthesiologist extends ListRecords
{
    protected static string $resource = AnesthesiologistResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
