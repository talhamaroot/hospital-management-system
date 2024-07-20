<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\Receptionist\Resources\AnesthesiologistLedgerResource\Pages;
use App\Filament\Receptionist\Resources\AnesthesiologistLedgerResource\RelationManagers;
use App\Models\Ledger;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnesthesiologistLedgerResource extends Resource
{
    protected static ?string $model = Ledger::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('anesthesiologist.name')
                    ->label('Attendant Name'),
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
                    ->summarize(Tables\Columns\Summarizers\Summarizer::make()->label('Balance')->money('PKR')
                        ->using(fn(\Illuminate\Database\Query\Builder $query) => $query->sum('debit') - $query->sum('credit'))
                    )
                    ->label('Created At')
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->inlineLabel(),
                        Forms\Components\DatePicker::make('created_until')->inlineLabel(),
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
                    })->columnSpan(3)->columns(2)
            ] , layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
//                Tables\Actions\EditAction::make(),
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
            'ledger' => Pages\ListAnesthesiologistLedgers::route('/{record}/ledgers'),
        ];
    }
}
