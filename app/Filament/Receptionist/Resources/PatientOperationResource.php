<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\Resources\PatientOperationResource\Pages;
use App\Models\Doctor;
use App\Models\DoctorOperation;
use App\Models\PatientOperation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PatientOperationResource extends Resource
{
    protected static ?string $model = PatientOperation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('patient_id')
                        ->relationship('patient', 'name')
                        ->searchable()
                        ->label('Patient'),
                    Forms\Components\Select::make("doctor_id")
                        ->options(Doctor::all()->pluck("name", "id"))
                        ->label('Doctor')
                        ->live()

                        ->dehydrated(false),
                    Forms\Components\Select::make('doctor_operation_id')

                        ->options(fn(Forms\Get $get) => \App\Models\DoctorOperation::where('doctor_id', $get('doctor_id'))->with('operation')->get()->pluck('operation.name', 'id'))
                        ->hidden(fn(Forms\Get $get) => !$get('doctor_id'))
                        ->live()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $doctorOperationId = $get('doctor_operation_id');
                            $doctorOperation = DoctorOperation::find($doctorOperationId);

                            // Set default fee for the patient based on selected doctor
                            if ($doctorOperation) {
                                $set('price', $doctorOperation->price);
                            }
                        })
                        ->searchable()
                        ->label('Doctor Operation'),
                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->type('number')
                        ->required(),
                    Forms\Components\TextInput::make('paid')
                        ->label('Paid')
                        ->type('number')
                        ->required(),

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
                Tables\Columns\TextColumn::make('doctorOperation.doctor.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctorOperation.operation.name')
                    ->label('Operation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid')
                    ->label('Paid')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => $record->status ? 'success' : 'warning')
                    ->getStateUsing(
                        fn($record) => $record->status ? 'Operation Completed' : 'Operation Pending'
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('patient_id')
                    ->relationship('patient', 'name')
                    ->label("Patient")

                    ->searchable(),

                Tables\Filters\SelectFilter::make('doctor_id')
                    ->label("Doctor")
                    ->relationship('doctor', 'name'),
                Tables\Filters\SelectFilter::make("status")
                    ->options([
                        "0" => "Pending",
                        "1" => "Completed",
                    ])
                    ->default("0"),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make("complete")
                    ->label("Mark as Complete")
                    ->color("success")
                    ->icon('heroicon-o-check-circle')

                    ->form([
                        Forms\Components\TextInput::make('expense')
                            ->label('Expense')
                            ->type('number')
                            ->required(),

                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'status' => 1,
                            'expense' => $data['expense'],
                        ]);
                        $record->createLedgerEntry();
                    })
                    ->hidden(fn($record) => $record->status),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientOperations::route('/'),
            'create' => Pages\CreatePatientOperation::route('/create'),
            'edit' => Pages\EditPatientOperation::route('/{record}/edit'),
        ];
    }
}
