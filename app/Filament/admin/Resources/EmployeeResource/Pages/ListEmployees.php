<?php

namespace App\Filament\admin\Resources\EmployeeResource\Pages;

use App\Filament\admin\Resources\EmployeeResource;
use App\Models\Ledger;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make("salary")
                ->label("Post Salary")
                ->form([
                    DatePicker::make("month")
                        ->format("F Y")
                        ->displayFormat("F Y"),
                    Repeater::make("salaries")
                        ->schema([
                            Select::make("employee_id")
                                ->options(fn() => \App\Models\Employee::pluck('name', 'id'))
                                ->label("Employee")
                                ->required(),
                            TextInput::make("salary")
                                ->label("Salary")
                                ->required()
                                ->type("number"),
                        ])->columns(2)->default(function () {
                            $employees = \App\Models\Employee::query()->where("status", "active")->get();
                            return $employees->map(function ($employee) {
                                return [
                                    "employee_id" => $employee->id,
                                    "salary" => $employee->salary
                                ];
                            });
                        })
                ])
                ->action(function (array $data) {

                    $month = $data["month"];
                    $salaries = $data["salaries"];
                    $salaryAmount = 0;
                    foreach ($salaries as $salary) {
                        Ledger::make([
                            "employee_id" => $salary["employee_id"],
                            "description" => "Salary for " . $month,
                            "debit" => $salary["salary"],
                            "credit" => 0
                        ])->save();
                        $salaryAmount += $salary["salary"];
                    }
                    Ledger::make([
                        "account" => "expense",
                        "description" => "Salary for " . $month,
                        "debit" => 0,
                        "credit" => $salaryAmount
                    ])->save();
                }),

        ];
    }
}
