<?php

namespace App\Filament\admin\Resources\AnesthesiologistResource\Pages;

use App\Filament\admin\Resources\AnesthesiologistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnesthesiologists extends ListRecords
{
    protected static string $resource = AnesthesiologistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
