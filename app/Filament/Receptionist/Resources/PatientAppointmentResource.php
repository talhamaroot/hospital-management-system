<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\receptionist\Resources\PatientAppointmentResource\Pages;
use App\Models\Ledger;
use App\Models\PatientAppointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                Tables\Actions\Action::make("complete")
                    ->label("Return Amount")
                    ->color("success")
                    ->icon('heroicon-o-currency-dollar')

                    ->form([
                        Forms\Components\TextInput::make('return_amount')
                            ->label('Return Amount')
                            ->type('number')
                            ->required(),

                    ])
                    ->action(function (array $data,  $record) {
                        Ledger::create([
                            'patient_id' => $record->patient_id,

                            'debit' => $data['return_amount'],
                            'credit' => $data['return_amount'],
                            'description' => 'Return Amount From Appointment with Dr. ' . $record->doctor->name,

                        ]);
                        Ledger::create([
                            "doctor_id" => $record->doctor_id,
                            "debit" => 0,
                            "credit" => $record->doctor->outdoor_sharing / 100 * $data['return_amount'],
                            "description" => "Return Amount Outdoor sharing from patient " . $record->patient->name,
                        ]);
                        Ledger::create([
                            "account" => "revenue",
                            "debit" => 0,
                            "credit" => (100 - $record->doctor->outdoor_sharing) / 100 * $data['return_amount'],
                            "description" => "Return Amount Outdoor sharing from patient " .$record->patient->name,
                        ]);
                    }),
                Tables\Actions\Action::make("print_receipt")
                    ->label("Print Receipt")


                   ->url(function ($record) {
                       $url = PatientAppointmentResource::getUrl("index");
                       return "/patient_recepiet/{$record->id}?redirectUrl=$url";
                   })


            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => PatientAppointmentResource\Pages\ListPatientAppointments::route('/'),
            'create' => PatientAppointmentResource\Pages\CreatePatientAppointment::route('/create'),
            'edit' => PatientAppointmentResource\Pages\EditPatientAppointment::route('/{record}/edit'),
        ];
    }
}
