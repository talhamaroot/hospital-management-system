<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anesthesiologist extends Model
{
    use HasFactory;

    protected $table = 'anesthesiologist';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'operation_fee',
    ];


    public function patientOperations() : HasMany
    {
        return $this->hasMany(PatientOperation::class);
    }

    public function ledgers() : HasMany
    {
        return $this->hasMany(Ledger::class);
    }
}
