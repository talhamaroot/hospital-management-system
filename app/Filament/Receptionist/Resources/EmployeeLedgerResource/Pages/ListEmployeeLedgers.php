<?php

namespace App\Filament\Receptionist\Resources\EmployeeLedgerResource\Pages;

use App\Filament\Receptionist\Resources\DoctorResource;
use App\Filament\Receptionist\Resources\EmployeeLedgerResource;
use App\Models\Ledger;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeLedgers extends ListRecords
{
    protected static string $resource = EmployeeLedgerResource::class;

    public $employeeId;

    public function mount(): void
    {
        $this->employeeId = request()->route('record');
    }


    public function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->where('employee_id', $this->employeeId);
    }


    public function getBreadcrumbs(): array
    {


        $resource = static::getResource();
        $breadcrumb = $this->getBreadcrumb();
        return [
            'Employee',
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
                        Select::make("employee_id")
                            ->disabled()
                            ->options(fn() => \App\Models\Employee::pluck('name', 'id'))
                            ->default($this->employeeId)
                        ,
                        TextInput::make("description")
                            ->label("Description")
                            ->required(),
                        TextInput::make("previous_balance")
                            ->label("Previous Balance")
                            ->readOnly()
                            ->default(function () {
                                $employee_id = $this->employeeId;
                                // check if ledger balance is zero
                                $ledger = Ledger::where('employee_id', $employee_id)->sum('debit') - Ledger::where('employee_id', $employee_id)->sum('credit');
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
                    $ledger->employee_id = $this->employeeId;
                    $ledger->description = $data['description'];
                    $ledger->debit = $data['debit'];
                    $ledger->credit = $data['credit'];
                    $ledger->save();


                })
            ,
            Actions\Action::make('employee_ledger_report')
                ->label("Print Employee Ledger Report")

                ->url(function () {
                    return url("/employee_ledger_report" . "/" . $this->employeeId);
                })
                ->openUrlInNewTab()
            ,
        ];
    }
}
