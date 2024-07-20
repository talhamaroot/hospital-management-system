<?php

namespace App\Filament\admin\Resources;

use App\Filament\admin\Resources\DoctorResource\Pages;
use App\Filament\admin\Resources\DoctorResource\RelationManagers;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone'),
                    Forms\Components\TextInput::make('address')
                        ->label('Address'),
                    Forms\Components\TextInput::make('specialization')
                        ->label('Specialization')
                        ->required(),
                    Forms\Components\TextInput::make('qualification')
                        ->label('Qualification')
                        ->required(),
                    Forms\Components\TextInput::make('experience')
                        ->label('Experience')
                        ->required(),
                    Forms\Components\TextInput::make('outdoor_fee')
                        ->type('number')
                        ->label('Fees')
                        ->required(),
                    Forms\Components\TextInput::make('outdoor_sharing')
                        ->type('number')
                        ->label('Outdoor Sharing')
                        ->required(),
                    Forms\Components\TextInput::make('operation_sharing')
                        ->type('number')
                        ->label('Operation Sharing')
                        ->required(),
                    Forms\Components\TextInput::make("referred_operation_sharing")
                        ->type('number')
                        ->label('Referred Operation Sharing')
                        ->required(),



                ])->columns(2),
                Forms\Components\Section::make([
                    Forms\Components\Repeater::make('operation')
                    ->relationship('operation')
                    ->schema([
                        Forms\Components\Select::make('operation_id')
                            ->label('Operation')
                            ->relationship('operation', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->type('number')
                            ->required(),

                    ])->columns(2)
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')

                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('outdoor_fee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('outdoor_sharing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operation_sharing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referred_operation_sharing')
                    ->searchable()
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('View Doctor')
                ->icon('heroicon-o-eye')
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
            DoctorResource\RelationManagers\LedgerRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => DoctorResource\Pages\ListDoctors::route('/'),
            'create' => DoctorResource\Pages\CreateDoctor::route('/create'),
            'edit' => DoctorResource\Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
