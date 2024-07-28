<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\receptionist\Resources\LedgerResource\Pages;
use App\Filament\receptionist\Resources\LedgerResource\RelationManagers;
use App\Models\Ledger;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LedgerResource extends Resource
{
    protected static ?string $model = Ledger::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make("user_type")
                        ->options([
                            'employee' => 'Employee',
                            'patient' => 'Patient',
                            'doctor' => 'Doctor',
                            "ot attendant" => "OT Attendant",
                            "aneathesiologist" => "Anesthesiologist",
                            'system' => 'Hospital',
                        ])
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $set('employee_id', null);
                            $set('patient_id', null);
                            $set('doctor_id', null);
                            $set('ot_attendant_id', null);
                            $set('anesthesiologist_id', null);

                        })
                        ->live()
                        ->dehydrated(false),
                    Forms\Components\Select::make("employee_id")
                        ->relationship('employee', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'employee')
                        ->searchable()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $employee = \App\Models\Employee::find($get('employee_id'));
                            $set('previous_balance', Ledger::where("employee_id", $employee->id)->sum('debit') - Ledger::where("employee_id", $employee->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("patient_id")
                        ->relationship('patient', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'patient')
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} (Phone : {$record->phone} )")
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $patient = \App\Models\Patient::find($get('patient_id'));
                            $set('previous_balance', Ledger::where("patient_id", $patient->id)->sum('debit') - Ledger::where("patient_id", $patient->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("doctor_id")
                        ->relationship('doctor', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'doctor')
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $doctor = \App\Models\Doctor::find($get('doctor_id'));
                            $set('previous_balance', Ledger::where("doctor_id", $doctor->id)->sum('debit') - Ledger::where("doctor_id", $doctor->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("ot_attendant_id")
                        ->relationship('otAttendant', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'ot attendant')
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $otAttendant = \App\Models\OTAttendant::find($get('ot_attendant_id'));
                            $set('previous_balance', Ledger::where("ot_attendant_id", $otAttendant->id)->sum('debit') - Ledger::where("ot_attendant_id", $otAttendant->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("anesthesiologist_id")
                        ->relationship('anesthesiologist', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'anesthesiologist')
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $anesthesiologist = \App\Models\Anesthesiologist::find($get('anesthesiologist_id'));
                            $set('previous_balance', Ledger::where("anesthesiologist_id", $anesthesiologist->id)->sum('debit') - Ledger::where("anesthesiologist_id", $anesthesiologist->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("account")
                        ->options([
                            // "capital" => "Capital",
                            "expense" => "Expense",
                            "revenue" => "Revenue",
                            "ot expense" => "OT Expense",
                        ])
                        ->label('Account')
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $set('previous_balance', Ledger::where("account", $get('account'))->sum('debit') - Ledger::where("account", $get('account'))->sum('credit'));
                        })
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'system')
                        ->required(),
                    Forms\Components\TextInput::make("previous_balance")
                        ->label('Previous Balance')
                        ->type('number')
                        ->default(0)
                        ->disabled(),

                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->required(),
                    Forms\Components\TextInput::make('debit')
                        ->label('Amount Received')
                        ->default(0)
                        ->type('number'),
                    Forms\Components\TextInput::make('credit')
                        ->label('Amount Paid')
                        ->default(0)
                        ->type('number'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("user")
                    ->label('Account Name')
                    ->width('1/4')
                    ->getStateUsing(
                        function ($record) {
                            if ($record->patient_id) {
                                return $record->patient->name . " (Patient)";
                            }
                            if ($record->employee_id) {
                                return $record->employee->name . " (Employee)";
                            }
                            if ($record->doctor_id) {
                                return $record->doctor->name . " (Doctor)";
                            }
                            if ($record->account) {
                                return $record->account . " (Hospital)";
                            }
                            if ($record->ot_attendant_id) {
                                return $record->otAttendant->name . " (OT Attendant)";
                            }
                            if ($record->anesthesiologist_id) {
                                return $record->anesthesiologist->name . " (Anesthesiologist)";
                            }
                        }
                    ),


                Tables\Columns\TextColumn::make('description')
                   
                    ->label('Description'),
                Tables\Columns\TextColumn::make('debit')
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('PKR'),
                    )
                    ->label('Debit'),
                Tables\Columns\TextColumn::make('credit')
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('PKR'),
                    )
                    ->label('Credit'),
                Tables\Columns\TextColumn::make('created_at')
        
                    ->dateTime('M d, Y h:i A')
                    ->summarize(Tables\Columns\Summarizers\Summarizer::make()->label('Balance')->money('PKR')
                        ->using(fn(\Illuminate\Database\Query\Builder $query) => $query->sum('debit') - $query->sum('credit'))
                    )
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Account Type')
                    ->form([
                        Forms\Components\Select::make('account_type')
                            ->options([
                                'employee' => 'Employee',
                                'patient' => 'Patient',
                                'doctor' => 'Doctor',
                                "ot attendant" => "OT Attendant",
                                "anesthesiologist" => "Anesthesiologist",
                                'system' => 'Hospital',
                            ])
                            ->label('Account Type')
                            ->inlineLabel()
                            ->required(),
                        Forms\Components\Select::make('patient_id')
                            ->relationship('patient', 'name')
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'patient')
                            ->searchable()
                            ->inlineLabel()
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->name} ({$record->phone})")
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'name')
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'employee')
                            ->searchable()
                            ->inlineLabel()
                            ->required(),
                        Forms\Components\Select::make('doctor_id')
                            ->relationship('doctor', 'name')
                            ->inlineLabel()
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'doctor')
                            ->required(),
                        Forms\Components\Select::make('ot_attendant_id')
                            ->relationship('otAttendant', 'name')
                            ->inlineLabel()
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'ot attendant')
                            ->required(),
                        Forms\Components\Select::make('anesthesiologist_id')
                            ->relationship('anesthesiologist', 'name')
                            ->inlineLabel()
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'anesthesiologist')
                            ->required(),
                        Forms\Components\Select::make('account')
                            ->options([
//                                "capital" => "Capital",
                                "expense" => "Expense",
                                "revenue" => "Revenue",
                                "ot expense" => "OT Expense",
                                  "treatment cost" => "Treatment Cost"
                            ])
                            ->label('Account')
                            ->inlineLabel()
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'system')
                            ->required(),
                            Forms\Components\DatePicker::make('created_from')->inlineLabel(),
                            Forms\Components\DatePicker::make('created_until')->inlineLabel(),
                          

                    ])
                    ->query(
                        function (Builder $query, array $data) {
                            if ($data['account_type']) {
                                if ($data['account_type'] == 'patient') {
                                    $query->where('patient_id',"!=" , null);
                                    $query->where('patient_id', $data['patient_id']);
                                }
                                if ($data['account_type'] == 'employee') {
                                    $query->where('employee_id',"!=" , null);
                                    $query->where('employee_id', $data['employee_id']);
                                }
                                if ($data['account_type'] == 'doctor') {
                                    $query->where('doctor_id',"!=" , null);
                                    $query->where('doctor_id', $data['doctor_id']);
                                }
                                if ($data['account_type'] == 'system') {
                                    $query->where('account',"!=" , null);
                                    $query->where('account', $data['account']);
                                }
                                if ($data['account_type'] == 'ot attendant') {
                                    $query->where('ot_attendant_id',"!=" , null);
                                    $query->where('ot_attendant_id', $data['ot_attendant_id']);
                                }
                                if ($data['account_type'] == 'anesthesiologist') {
                                    $query->where('anesthesiologist_id',"!=" , null);
                                    $query->where('anesthesiologist_id', $data['anesthesiologist_id']);
                                }
                             

                            }
                            $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                        }
                        )->columnSpan(5)->columns(4)
                        ] , layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => LedgerResource\Pages\ListLedgers::route('/'),
//            'create' => Pages\CreateLedger::route('/create'),
//            'edit' => Pages\EditLedger::route('/{record}/edit'),
        ];
    }
}
