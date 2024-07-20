<?php

namespace App\Filament\owner\Resources\LedgerResource\Pages;

use App\Filament\admin\Resources\LedgerResource;
use App\Models\Ledger;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListLedgers extends ListRecords
{
    protected static string $resource = LedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\Action::make("get_payment")
                ->label("Get Payment")
                ->icon('heroicon-o-banknotes')
                ->form([
                    Section::make([
                        TextInput::make("amount")
                            ->label("Amount")
                            ->required()
                            ->type('number')
                            ->default(fn () => Ledger::query()->sum('debit') - Ledger::query()->sum('credit')),
                    ])->columns(2)
                ])
                ->action(
                    function (array $data){
                        Ledger::create([
                            "account" => "capital",
                            "description" => "Owner Withdrawal",
                            "debit" => 0,
                            "credit" =>$data["amount"],
                        ]);
                    }
                )
        ];
    }
}
