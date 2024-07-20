<?php

namespace App\Filament\Receptionist\Resources\PatientAppointmentResource\Pages;

use App\Filament\receptionist\Resources\PatientAppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientAppointment extends CreateRecord
{
    protected static string $resource = PatientAppointmentResource::class;

    protected function getRedirectUrl() : string
    {
        return static::getResource()::getUrl('index');
    }
}
