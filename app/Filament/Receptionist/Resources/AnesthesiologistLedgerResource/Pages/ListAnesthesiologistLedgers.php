<?php

namespace App\Filament\Receptionist\Resources\AnesthesiologistLedgerResource\Pages;

use App\Filament\Receptionist\Resources\AnesthesiologistLedgerResource;
use App\Filament\Receptionist\Resources\EmployeeLedgerResource;
use App\Filament\Receptionist\Resources\OTAttendantLedgerResource;
use App\Models\Ledger;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListAnesthesiologistLedgers extends ListRecords
{
    protected static string $resource = AnesthesiologistLedgerResource::class;

    public $anesAttendantId;

    public function mount(): void
    {
        $this->anesAttendantId = request()->route('record');
    }


    public function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->where('anesthesiologist_id', $this->anesAttendantId);
    }


    public function getBreadcrumbs(): array
    {


        $resource = static::getResource();
        $breadcrumb = $this->getBreadcrumb();
        return [
            'Anesthesiologist',
            'Anesthesiologist Ledgers',
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
                        Select::make("anesthesiologist_id")
                            ->disabled()
                            ->options(fn() => \App\Models\Anesthesiologist::pluck('name', 'id'))
                            ->default($this->anesAttendantId)
                        ,
                        TextInput::make("description")
                            ->label("Description")
                            ->required(),
                        TextInput::make("previous_balance")
                            ->label("Previous Balance")
                            ->readOnly()
                            ->default(function () {
                                $anesthesiologist_id = $this->anesAttendantId;
                                // check if ledger balance is zero
                                $ledger = Ledger::where('anesthesiologist_id', $anesthesiologist_id)->sum('debit') - Ledger::where('anesthesiologist_id', $anesthesiologist_id)->sum('credit');
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
                    $ledger->anesthesiologist_id = $this->anesAttendantId;
                    $ledger->description = $data['description'];
                    $ledger->debit = $data['debit'];
                    $ledger->credit = $data['credit'];
                    $ledger->save();


                })
            ,
            Actions\Action::make('attendant_ledger_report')
                ->label("Print Anesthesiologist Ledger Report")

                ->url(function () {
                    return url("/anesthesiologist_ledger_report" . "/" . $this->anesAttendantId);
                })
                ->openUrlInNewTab()
            ,
        ];
    }
}
