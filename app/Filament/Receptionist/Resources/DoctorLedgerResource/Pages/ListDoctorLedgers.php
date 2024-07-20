<?php

namespace App\Filament\Receptionist\Resources\DoctorLedgerResource\Pages;

use App\Filament\Receptionist\Resources\DoctorLedgerResource;
use App\Filament\Receptionist\Resources\DoctorResource;
use App\Filament\Receptionist\Resources\PatientLedgerResource;
use App\Filament\Receptionist\Resources\PatientResource;
use App\Models\Ledger;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\ListRecords;

class ListDoctorLedgers extends ListRecords
{
    protected static string $resource = DoctorLedgerResource::class;


    public $doctorId;

    public function mount(): void
    {
        $this->doctorId = request()->route('record');
    }


    public function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->where('doctor_id', $this->doctorId);
    }


    public function getBreadcrumbs(): array
    {
        $baseUrl = DoctorResource::getUrl('index');

        $resource = static::getResource();
        $breadcrumb = $this->getBreadcrumb();
        return [
            $baseUrl => 'Doctor',
            'Patient Ledgers',
            ...(filled($breadcrumb) ? [$breadcrumb] : []),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\Action::make('ledger')
                ->label("Confirm Payment")
                ->action(null)
                ->hidden(fn() => Ledger::where('doctor_id', $this->doctorId)->sum('debit') - Ledger::where('doctor_id', $this->doctorId)->sum('credit') == 0)
                ->form([
                    Select::make("doctor_id")
                        ->relationship("doctor", "name")
                        ->label('Doctor')
                        ->disabled()
                        ->default($this->doctorId),
                    TextInput::make('description')
                        ->label('Description')
                        ->readOnly()
                        ->default("Paid to Doctor"),
                    TextInput::make('credit')
                        ->label('Total Amount')
                        ->default(function () {
                            return Ledger::query()->where('doctor_id', $this->doctorId)->sum('debit') - Ledger::query()->where('doctor_id', $this->doctorId)->sum('credit');
                        })
                        ->readOnly()
                        ->type('number')
                        ->required(),
                    TextInput::make("image_url")
                        ->label('')
                        ->type("hidden")
                        ->readOnly(),
                    SpatieMediaLibraryFileUpload::make("image")
                        ->label('Image')
                        ->image()
                        ->afterStateUpdated(fn(Set $set , Get $get) => $set('image_url', collect($get('image'))->first()->getRealPath()))
                        ->maxFiles(1)
                        ->required(),
                ])
                ->action(function (array $data) {
                    //create ledger Entry with fileupload

                    $ledger = Ledger::create([
                        'doctor_id' => $this->doctorId,
                        'description' => $data['description'],
                        'credit' => $data['credit'],
                        'debit' => 0,
                    ]);
                    $ledger->addMedia($data['image_url'])->toMediaCollection('images');

                })

            ,
            Actions\Action::make('print_doctor_report')
                ->label("Print Daily Report")
                ->hidden(fn() => Ledger::where('doctor_id', $this->doctorId)->sum('debit') - Ledger::where('doctor_id', $this->doctorId)->sum('credit') == 0)
                ->url(function () {
                    return url("/print_doctor_report" . "/" . $this->doctorId);
                })
                ->openUrlInNewTab()
            ,
        ];
    }
}
