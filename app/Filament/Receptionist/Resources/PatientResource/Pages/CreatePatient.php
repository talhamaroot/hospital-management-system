<?php

namespace App\Filament\Receptionist\Resources\PatientResource\Pages;

use App\Filament\receptionist\Resources\PatientResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function getRedirectUrl() : string
    {
        $resource = static::getResource();
        $url = $resource::getUrl('index');
        $record = $this->getRecord();
        return "/patient_recepiet/{$record->appointment[0]->id}?redirectUrl=$url";
    }

}
