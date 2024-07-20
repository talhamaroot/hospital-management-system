<?php

namespace App\Filament\admin\Resources\DoctorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LedgerRelationManager extends RelationManager
{
    protected static string $relationship = 'ledger';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->required(),
                Forms\Components\TextInput::make('credit')
                    ->label('Amount Paid')
                    ->type('number')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
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
                    ->summarize(Tables\Columns\Summarizers\Summarizer::make()->label('Balance')->money('PKR')
                        ->using(fn (\Illuminate\Database\Query\Builder $query) => $query->sum('debit') - $query->sum('credit'))
                    )
                    ->label('Created At')
                ,
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
