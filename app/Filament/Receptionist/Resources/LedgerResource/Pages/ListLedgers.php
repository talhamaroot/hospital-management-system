<?php

namespace App\Filament\Receptionist\Resources\LedgerResource\Pages;

use App\Filament\receptionist\Resources\LedgerResource;
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
            Actions\Action::make("print")
            ->url(function () {
                $data = $this->tableFilters;
                $account_type = $data['user']['account_type'] ? $data['user']['account_type']:"null" ;
                $account_id = "null";
                if ($account_type == 'patient') {
                    $account_id = $data['user']["patient_id"];
                }
                if ($account_type == 'employee') {
                    $account_id = $data['user']["employee_id"];
                }
                if ($account_type == 'doctor') {
                    $account_id = $data['user']["doctor_id"];
                }
                if ($account_type == 'system') {
                    $account_id = $data['user']["account"];
                }
                if ($account_type == 'ot attendant') {
                    $account_id = $data['user']["ot_attendant_id"];
                }
                if ($account_type == 'anesthesiologist') {
                    $account_id = $data['user']["anesthesiologist_id"];
                }
                $date_from = $data['user']["created_from"] ? $data['user']["created_from"] : "null";
                $date_to = $data['user']["created_until"] ? $data['user']["created_until"]  : "null";
            
                return (url("/print_ledger/$account_type/$account_id/$date_from/$date_to" ));
            })
            ->openUrlInNewTab()

        ];
    }
}
