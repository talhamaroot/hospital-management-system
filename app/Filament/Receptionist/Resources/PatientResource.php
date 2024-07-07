<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make("name")
                        ->label("Name")
                        ->required(),

                    Forms\Components\TextInput::make("phone")
                        ->label("Phone")
                        ->required(),
                    Forms\Components\TextInput::make("address")
                        ->label("Address")
                        ->required(),
                    Forms\Components\TextInput::make("age")
                        ->label("Age")
                        ->required(),
                    Forms\Components\Select::make("gender")
                        ->label("Gender")
                        ->options([
                            "male" => "Male",
                            "female" => "Female",
                        ])
                        ->required(),

                    Forms\Components\TextInput::make("cnic")
                        ->label("CNIC")
                        ->mask('99999-9999999-9'),
                    Repeater::make("appointment")
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
                                ->required(),
                            Forms\Components\TextInput::make('paid')
                                ->label('Paid')
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LedgerRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
