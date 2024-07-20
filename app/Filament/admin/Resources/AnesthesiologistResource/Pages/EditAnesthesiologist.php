<?php

namespace App\Filament\admin\Resources\AnesthesiologistResource\Pages;

use App\Filament\admin\Resources\AnesthesiologistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnesthesiologist extends EditRecord
{
    protected static string $resource = AnesthesiologistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
