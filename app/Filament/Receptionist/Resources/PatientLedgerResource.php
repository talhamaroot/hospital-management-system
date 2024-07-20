<?php

namespace App\Filament\Receptionist\Resources;

use App\Filament\Receptionist\Resources\PatientLedgerResource\Pages;
use App\Filament\Receptionist\Resources\PatientLedgerResource\RelationManagers;
use App\Models\Ledger;
use App\Models\PatientLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientLedgerResource extends Resource
{

    protected static ?string $model = Ledger::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $breadcrumb = "";

    public function __construct()
    {
        parent::__construct();

        // Assign the route parameter 'record' to $this->record
        $this->record = request()->route('record');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }



    public static function table(Table $table): Table
    {
        $patientId = request()->route('record');
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')

                    ->label('Patient Name'),
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
                        ->using(fn (\Illuminate\Database\Query\Builder $query) => $query->sum('debit') - $query->sum('credit'))
                    )
                    ->label('Created At')
            ])

            ->filters([
                //
            ])
            ->defaultSort('id', 'desc')
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

                "ledger" => Pages\ListPatientLedgers::route("/{record}/ledger"),
        ];
    }
}
