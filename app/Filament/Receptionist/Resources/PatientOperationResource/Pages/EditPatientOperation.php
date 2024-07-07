<?php

namespace App\Filament\Receptionist\Resources\PatientOperationResource\Pages;

use App\Filament\Resources\PatientOperationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatientOperation extends EditRecord
{
    protected static string $resource = PatientOperationResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);


        $this->authorizeAccess();

        $this->fillForm();

        $this->form->fill([
            "doctor_id" => $this->record->doctorOperation->doctor_id,
            "doctor_operation_id" => $this->record->doctor_operation_id,
            "price" => $this->record->price,
            "paid" => $this->record->paid,

        ]);

        $this->previousUrl = url()->previous();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
