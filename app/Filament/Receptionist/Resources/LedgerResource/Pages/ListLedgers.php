<?php

namespace App\Filament\Receptionist\Resources\LedgerResource\Pages;

use App\Filament\Resources\LedgerResource;
use ArielMejiaDev\FilamentPrintable\Actions\PrintAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLedgers extends ListRecords
{
    protected static string $resource = LedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

        ];
    }
}