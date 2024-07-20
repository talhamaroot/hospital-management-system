<?php

namespace App\Filament\Receptionist\Resources\PatientLedgerResource\Pages;

use App\Filament\Receptionist\Resources\PatientLedgerResource;
use App\Filament\Receptionist\Resources\PatientResource;
use App\Models\Ledger;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use PhpParser\Builder;

class ListPatientLedgers extends ListRecords
{
    protected static string $resource = PatientLedgerResource::class;


    public $patientId;

    public function mount(): void
    {
        $this->patientId = request()->route('record');
    }


    public function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->where('patient_id', $this->patientId);
    }


    public function getBreadcrumbs(): array
    {
        $baseUrl = PatientResource::getUrl('index');

        $resource = static::getResource();
        $breadcrumb = $this->getBreadcrumb();
        return [
            $baseUrl => 'Patients',
            'Patient Ledgers',
            ...(filled($breadcrumb) ? [$breadcrumb] : []),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\Action::make('ledger')
                ->label("Add Ledger")
                ->action(null)
                ->form([
                    Section::make([
                        Select::make("patient_id")
                            ->disabled()
                            ->options(fn() => \App\Models\Patient::pluck('name', 'id'))
                            ->default($this->patientId)
                        ,
                        TextInput::make("description")
                            ->label("Description")
                            ->required(),
                        TextInput::make("previous_balance")
                            ->label("Previous Balance")
                            ->readOnly()
                            ->default(function () {
                                $patient_id = $this->patientId;
                                // check if ledger balance is zero
                                $ledger = Ledger::where('patient_id', $patient_id)->sum('debit') - Ledger::where('patient_id', $patient_id)->sum('credit');
                                return $ledger;

                            }),

                        TextInput::make("debit")
                            ->label("Received Amount")
                            ->type("number")
                            ->default("0")
                            ->required(),
                        TextInput::make("credit")
                            ->label("Paid Amount")
                            ->default("0")
                            ->type("number")
                            ->required(),
                    ])->columns(2)
                ])
                ->action(function ($data) {


                    $ledger = new Ledger();
                    $ledger->patient_id = $this->patientId;
                    $ledger->description = $data['description'];
                    $ledger->debit = $data['debit'];
                    $ledger->credit = $data['credit'];
                    $ledger->save();


                })
            ,
            Actions\Action::make('discharge')
                ->label("Print Discharge Summary")
                ->hidden(function () {
                    $patient_id = $this->patientId;
                    // check if ledger balance is zero
                    $ledger = Ledger::where('patient_id', $patient_id)->sum('debit') - Ledger::where('patient_id', $patient_id)->sum('credit');
                    return $ledger != 0;

                })
                ->url(function () {
                    return url("/patient_discharge" . "/" . $this->patientId);
                })
                ->openUrlInNewTab()
            ,
        ];
    }
}
