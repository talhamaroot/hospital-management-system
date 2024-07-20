<?php

namespace App\Filament\admin\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttandenceRelationManager extends RelationManager
{
    protected static string $relationship = 'attandence';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
//                Forms\Components\DateTimePicker::make('time_in')
//                    ->required()
//                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('time_in')
            ->columns([
                Tables\Columns\TextColumn::make('time_in')->dateTime(),
                Tables\Columns\TextColumn::make('time_out')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make("date_from_to")
                    ->form([
                        Forms\Components\DatePicker::make("time_in_from")
                            ->label("Date From")->required(),
                        Forms\Components\DatePicker::make("time_in_to")
                            ->label("Date To")->required(),
                    ])->query(
                        function (Builder $query, array $data) {
                            if ($data['time_in_from'] && $data['time_in_to']) {
                                $query->whereBetween('time_in', [$data['time_in_from'], $data['time_in_to']]);
                            }
                        }
                    ),
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
