<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\Resources\LedgerResource\Pages;
use App\Filament\Resources\LedgerResource\RelationManagers;
use App\Models\Ledger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            'system' => 'System',
                        ])
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $set('employee_id', null);
                            $set('patient_id', null);
                            $set('doctor_id', null);

                        })
                        ->live()
                        ->dehydrated(false),
                    Forms\Components\Select::make("employee_id")
                        ->relationship('employee', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'employee')
                        ->searchable()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $employee = \App\Models\Employee::find($get('employee_id'));
                            $set('previous_balance', Ledger::where("employee_id" , $employee->id)->sum('debit') - Ledger::where("employee_id" , $employee->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("patient_id")
                        ->relationship('patient', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'patient')
                        ->searchable()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $patient = \App\Models\Patient::find($get('patient_id'));
                            $set('previous_balance', Ledger::where("patient_id" , $patient->id)->sum('debit') - Ledger::where("patient_id" , $patient->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("doctor_id")
                        ->relationship('doctor', 'name')
                        ->hidden(fn(Forms\Get $get) => $get('user_type') !== 'doctor')
                        ->searchable()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $doctor = \App\Models\Doctor::find($get('doctor_id'));
                            $set('previous_balance', Ledger::where("doctor_id" , $doctor->id)->sum('debit') - Ledger::where("doctor_id" , $doctor->id)->sum('credit'));
                        })
                        ->live(),
                    Forms\Components\Select::make("account")
                        ->options([
                            "capital" => "Capital",
                            "expense" => "Expense",
                            "revenue" => "Revenue",
                        ])
                        ->label('Account')
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $set('previous_balance', Ledger::where("account" , $get('account'))->sum('debit') - Ledger::where("account" , $get('account'))->sum('credit'));
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
                            if($record->account){
                                return $record->account . " (System)";
                            }
                        }
                    ),


                Tables\Columns\TextColumn::make('description')
                    ->searchable()
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
                    ->searchable()
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
                                'system' => 'System',
                            ])
                            ->label('Account Type')

                            ->required(),
                        Forms\Components\Select::make('patient_id')
                            ->relationship('patient', 'name')
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'patient')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'name')
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'employee')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('doctor_id')
                            ->relationship('doctor', 'name')
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'doctor')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('account')
                            ->options([
                                "capital" => "Capital",
                                "expense" => "Expense",
                                "revenue" => "Revenue",
                            ])
                            ->label('Account')
                            ->hidden(fn(Forms\Get $get) => $get('account_type') !== 'system')
                            ->required(),

                    ])
                    ->query(
                        function (Builder $query ,array $data) {
                            if($data['account_type']){
                               if($data['account_type'] == 'patient'){
                                        $query->where('patient_id' , $data['patient_id']);
                                    }
                                    if($data['account_type'] == 'employee'){
                                        $query->where('employee_id' , $data['employee_id']);
                                    }
                                    if($data['account_type'] == 'doctor'){
                                        $query->where('doctor_id' , $data['doctor_id']);
                                    }
                                    if($data['account_type'] == 'system') {
                                        $query->where('account', $data['account']);
                                    }
                            }
                        }
                    ),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })



            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLedgers::route('/'),
//            'create' => Pages\CreateLedger::route('/create'),
//            'edit' => Pages\EditLedger::route('/{record}/edit'),
        ];
    }
}
