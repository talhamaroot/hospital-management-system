<?php

namespace App\Filament\Receptionist\Resources\PatientAppointmentResource\Pages;

use App\Filament\Resources\PatientAppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientAppointment extends CreateRecord
{
    protected static string $resource = PatientAppointmentResource::class;
}
