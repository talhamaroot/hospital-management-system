<?php

namespace App\Filament\Receptionist\Resources\PatientOperationResource\Pages;

use App\Filament\receptionist\Resources\PatientOperationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatientOperations extends ListRecords
{
    protected static string $resource = PatientOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
