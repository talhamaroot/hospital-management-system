<?php

namespace App\Filament\admin\Resources;

use App\Filament\admin\Resources\OTAttendantResource\Pages;
use App\Filament\admin\Resources\OTAttendantResource\RelationManagers;
use App\Models\OTAttendant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OTAttendantResource extends Resource
{
    protected static ?string $model = OTAttendant::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email'),
                    Forms\Components\TextInput::make('address')
                        ->label('Address'),
                    Forms\Components\TextInput::make('operation_fee')
                        ->label('Operation Fee')
                        ->type('number')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operation_fee')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOTAttendants::route('/'),
            'create' => Pages\CreateOTAttendant::route('/create'),
            'edit' => Pages\EditOTAttendant::route('/{record}/edit'),
        ];
    }
}
