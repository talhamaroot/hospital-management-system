<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientAppointmentResource\Pages;
use App\Models\PatientAppointment;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PatientAppointmentResource extends Resource
{
    protected static ?string $model = PatientAppointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('patient_id')
                        ->relationship('patient', 'name')
                        ->searchable()
                        ->label('Patient'),
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
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('patient_id')
                    ->relationship('patient', 'name')
                    ->label("Patient")
                    ->searchable(),

                SelectFilter::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->label("Doctor")
                    

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make("print_receipt")
                    ->label("Print Receipt")
                    ->modalContent(function ($record) {
                        return view('print_receipt', compact('record'));

                    })
                    ->icon('heroicon-o-printer')
                    ->modalFooterActions([
                        Tables\Actions\Action::make("print_receipt")
                            ->label("Print Receipt")

                            ->action(function ($livewire) {
                                $livewire->js('window.print();');
                            }),
                    ]),

            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientAppointments::route('/'),
            'create' => Pages\CreatePatientAppointment::route('/create'),
            'edit' => Pages\EditPatientAppointment::route('/{record}/edit'),
        ];
    }
}
