<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\receptionist\Resources\PatientOperationResource\Pages;
use App\Models\Doctor;
use App\Models\DoctorOperation;
use App\Models\PatientOperation;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} (Phone : {$record->phone} )")
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
                    Forms\Components\Select::make('ot_attendant_id')
                        ->relationship('otAttendant', 'name')
                        ->label('OT Attendant')
                        ->required(),
                    Forms\Components\Select::make("anesthesiologist_id")
                        ->relationship('anesthesiologist', 'name')
                        ->label('Anesthesiologist'),
                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->readOnly()
                        ->type('number')
                        ->required(),
                    Forms\Components\TextInput::make('paid')
                        ->label('Paid')
                        ->type('number')
                        ->required(),
                    Forms\Components\Select::make("referred_by")
                        ->options([
                            "hospital" => "Hospital",
                            "doctor" => "Doctor"
                        ])
                        ->default("hospital")
                        ->label("Referred By"),

                ])->columns(2)

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
                Tables\Columns\TextColumn::make("otAttendant.name")
                    ->label("OT Attendant")
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
                    ->relationship('doctorOperation.doctor', 'name'),
                Tables\Filters\SelectFilter::make("status")
                    ->options([
                        "0" => "Pending",
                        "1" => "Completed"
                    ])
                    ->default("0")
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make("view_image")
                    ->label("View Upload Picture")
                    ->hidden(fn($record) => !$record->getFirstMediaUrl('images'))
                    ->infolist([
                        SpatieMediaLibraryImageEntry::make('image')
                            ->collection('images')

                    ]),
                Tables\Actions\Action::make("complete")
                    ->label("Mark as Complete")
                    ->color("success")
                    ->icon('heroicon-o-check-circle')
                    ->form([
                        Forms\Components\TextInput::make('expense')
                            ->label('Expense')
                            ->type('number')
                            ->required(),

                        Forms\Components\TextInput::make('ot_attendant_expense')
                            ->label('OT Attendant Expense')
                            ->type('number')
                            ->default(fn ($record) => $record->otAttendant->operation_fee)
                            ->required(),

                        Forms\Components\TextInput::make('ot_anesthesiologist_expense')
                            ->label('OT Anesthesiologist Expense')
                            ->type('number')
                            ->hidden(fn($record) => !isset($record->anesthesiologist_id) )
                            ->default(fn ($record) => isset($record->anesthesiologist_id) ? $record->anesthesiologist->operation_fee : null)
                            ->required(),

                        SpatieMediaLibraryFileUpload::make("image")
                            ->label("Upload Image")
                            ->collection("images")
                            ->image()
                            ->maxFiles(1)
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {

                        $record->update([
                            'status' => 1,
                            'expense' => $data['expense'],
                        ]);


                        $record->createLedgerEntry($data['ot_attendant_expense'] , isset($data['ot_anesthesiologist_expense']) ? $data['ot_anesthesiologist_expense'] : null);
                    })
                    ->hidden(fn($record) => $record->status)

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => PatientOperationResource\Pages\ListPatientOperations::route('/'),
            'create' => PatientOperationResource\Pages\CreatePatientOperation::route('/create'),
            'edit' => PatientOperationResource\Pages\EditPatientOperation::route('/{record}/edit'),
        ];
    }
}
