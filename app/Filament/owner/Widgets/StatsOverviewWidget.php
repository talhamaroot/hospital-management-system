<?php

namespace App\Filament\owner\Widgets;

use App\Models\Ledger;
use App\Models\PatientOperation;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {

       $revenue = Ledger::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where("account" , "revenue")
            ->sum('debit' ) - Ledger::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where("account" , "revenue")
            ->sum('credit');
       $expense = Ledger::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where("account" , "expense")
            ->sum('credit') -
            Ledger::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where("account" , "expense")
            ->sum('debit');
         $operations = PatientOperation::query()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();


        return [
            Stat::make('Revenue Last 7 Days', 'PKR ' . $revenue)

                ->color('success'),
            Stat::make('Expense Last 7 Days', 'PKR ' . $expense)

                ->color('danger'),
            Stat::make('Operations Last 7 Days', $operations)

                ->color('success'),
        ];
    }
}
