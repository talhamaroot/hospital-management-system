<?php

namespace App\Filament\admin\Resources\EmployeeResource\Pages;

use App\Filament\admin\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
}
