<?php

namespace App\Filament\admin\Resources\UserResource\Pages;

use App\Filament\admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
