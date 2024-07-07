<?php

namespace App\Filament\Receptionist\Resources\PatientResource\Pages;

use App\Filament\Receptionist\Resources\PatientAppointmentResource;
use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function getRedirectUrl() : string
    {
        return PatientAppointmentResource::getUrl('index');
    }
}
