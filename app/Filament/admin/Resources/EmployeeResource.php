<?php

namespace App\Filament\admin\Resources;

use App\Filament\admin\Resources\EmployeeResource\Pages;
use App\Filament\admin\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('cnic')
                        ->label('CNIC')
                        ->mask('99999-9999999-9'),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone'),
                    Forms\Components\TextInput::make('address')
                        ->label('Address'),
                    Forms\Components\TextInput::make("city")
                        ->label("City")
                        ->required(),
                    Forms\Components\TextInput::make('designation')
                        ->label('Designation')
                        ->required(),
                    Forms\Components\TextInput::make('salary')
                        ->type('number')
                        ->label('Salary')
                        ->required(),
                    Forms\Components\Select::make("status")
                        ->label("Status")
                        ->options([
                            "active" => "Active",
                            "inactive" => "Inactive"
                        ])
                        ->required(),
                    Forms\Components\Select::make("biometric_id")
                        ->label("Biometric Employee")
                        ->relationship("biometricEmployee", "name")

                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('cnic')
                    ->searchable()
                    ->label('CNIC'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Phone'),
            ])
            ->filters([


            ])
            ->actions([
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
            EmployeeResource\RelationManagers\LedgerRelationManager::class,
            EmployeeResource\RelationManagers\AttandenceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => EmployeeResource\Pages\ListEmployees::route('/'),
            'create' => EmployeeResource\Pages\CreateEmployee::route('/create'),
            'edit' => EmployeeResource\Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
