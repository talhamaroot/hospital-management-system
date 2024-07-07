<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    public function appointment() : HasMany
    {
        return $this->hasMany(PatientAppointment::class);
    }

    public function ledger() : HasMany
    {
        return $this->hasMany(Ledger::class);
    }
}
