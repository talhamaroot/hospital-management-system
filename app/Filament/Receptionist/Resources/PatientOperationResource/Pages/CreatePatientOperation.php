<?php

namespace App\Filament\Receptionist\Resources\PatientOperationResource\Pages;

use App\Filament\Resources\PatientOperationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientOperation extends CreateRecord
{
    protected static string $resource = PatientOperationResource::class;
}
