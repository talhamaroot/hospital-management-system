<?php

namespace App\Filament\Receptionist\Resources;


use App\Models\Ledger;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Section::make("Patient Information")
                    ->schema([
                        Forms\Components\TextInput::make("name")
                        ->label("Name")
                        ->required(),

                    Forms\Components\TextInput::make("phone")
                        ->label("Phone")
                        ->required(),
                    Forms\Components\Select::make("gender")
                        ->label("Gender")
                        ->options([
                            "male" => "Male",
                            "female" => "Female",
                        ])
                        ->required(),
                    Forms\Components\TextInput::make("address")
                        ->label("Address")
                    // ->required()
                    ,
                    Forms\Components\TextInput::make("age")
                        ->label("Age"),


                    Forms\Components\TextInput::make("cnic")
                        ->label("CNIC")
                        ->mask('99999-9999999-9'),
                    ])->columns(2)->columnSpan(2),
                    Forms\Components\Repeater::make("appointment")
                        ->relationship("appointment")
                        ->schema([
                            Forms\Components\Select::make('doctor_id')
                                ->relationship('doctor', 'name')
                                ->live()
                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                    $doctorId = $get('doctor_id');
                                    $doctor = \App\Models\Doctor::find($doctorId);

                                    // Set default fee for the patient based on selected doctor
                                    if ($doctor) {
                                        $set('price', $doctor->outdoor_fee);
                                    }
                                })
                                ->label('Doctor'),
                            Forms\Components\TextInput::make('price')
                                ->label('Price')
                                ->type('number')
                                ->readOnly()
                                ->required(),
                            Forms\Components\TextInput::make('paid')
                                ->label('Payment Received')
                                ->type('number')
                                ->required(),
                            Forms\Components\TextInput::make('temperature')
                                ->label('Temperature'),
                            Forms\Components\TextInput::make('bp')
                                ->label('BP'),
                            Forms\Components\TextInput::make('weight')
                                ->label('Weight'),
                        ])->columns(2)->columnSpan(2),
                ])->columns(2),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')

                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label('Address'),
                Tables\Columns\TextColumn::make('age')
                    ->searchable()
                    ->label('Age'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make("treatmentcost")
                ->label("Treatment Cost")
                ->form([
                    TextInput::make("treatmentcost")
                    ->type("number")
                    ->required()

                ])
                ->action(function (array $data , $record) {
                    Ledger::create([
                        "account" => "treatment cost",
                        "debit" => $data["treatmentcost"],
                        "credit" => 0,
                        "description" => "Treatment cost Patient ".$record->name,
                    ]);
                    Ledger::create([
                        "patient_id" => $record->id,
                        "debit" => $data["treatmentcost"],
                        "credit" => $data["treatmentcost"],
                        "description" => "Treatment cost ",
                    ]);
                }),
                Tables\Actions\Action::make("View Ledger")
                ->url(fn ($record) => PatientLedgerResource::getUrl('ledger' , ["record" => $record] )),
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PatientResource\RelationManagers\LedgerRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => PatientResource\Pages\ListPatients::route('/'),
            'create' => PatientResource\Pages\CreatePatient::route('/create'),
            'edit' => PatientResource\Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
