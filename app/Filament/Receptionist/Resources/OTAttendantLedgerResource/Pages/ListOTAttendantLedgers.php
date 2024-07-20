<?php

namespace App\Filament\Receptionist\Resources\OTAttendantLedgerResource\Pages;

use App\Filament\Receptionist\Resources\EmployeeLedgerResource;
use App\Filament\Receptionist\Resources\OTAttendantLedgerResource;
use App\Models\Ledger;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\Layout;

class ListOTAttendantLedgers extends ListRecords
{
    protected static string $resource = OTAttendantLedgerResource::class;

    public $otAttendantId;

    public function mount(): void
    {
        $this->otAttendantId = request()->route('record');
    }




    public function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->where('ot_attendant_id', $this->otAttendantId);
    }


    public function getBreadcrumbs(): array
    {


        $resource = static::getResource();
        $breadcrumb = $this->getBreadcrumb();
        return [
            'Attendant',
            'Attendant Ledgers',
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
                        Select::make("ot_attendant_id")
                            ->disabled()
                            ->options(fn() => \App\Models\OTAttendant::pluck('name', 'id'))
                            ->default($this->otAttendantId)
                        ,
                        TextInput::make("description")
                            ->label("Description")
                            ->required(),
                        TextInput::make("previous_balance")
                            ->label("Previous Balance")
                            ->readOnly()
                            ->default(function () {
                                $ot_attendant_id = $this->otAttendantId;
                                // check if ledger balance is zero
                                $ledger = Ledger::where('ot_attendant_id', $ot_attendant_id)->sum('debit') - Ledger::where('ot_attendant_id', $ot_attendant_id)->sum('credit');
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
                    $ledger->ot_attendant_id = $this->otAttendantId;
                    $ledger->description = $data['description'];
                    $ledger->debit = $data['debit'];
                    $ledger->credit = $data['credit'];
                    $ledger->save();


                })
            ,
            Actions\Action::make('attendant_ledger_report')
                ->label("Print Attendant Ledger Report")

                ->url(function () {
                    return url("/attendant_ledger_report" . "/" . $this->otAttendantId);
                })
                ->openUrlInNewTab()
            ,
        ];
    }
}
