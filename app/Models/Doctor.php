<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;



    public function operation() : HasMany
    {
        return $this->hasMany(DoctorOperation::class);
    }

    public function appointment() : HasMany
    {
        return $this->hasMany(PatientAppointment::class);
    }

    public function ledger() : HasMany
    {
        return $this->hasMany(Ledger::class);
    }
}
