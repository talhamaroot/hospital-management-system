<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'cnic',
        'city',
        'designation',
        'salary',

    ];


    public function ledger() : HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    public function biometricEmployee() : BelongsTo
    {
        return $this->belongsTo(BiometricEmployee::class, 'biometric_id');
    }


    public function attandence() : HasMany
    {
        return $this->hasMany(Attandence::class);
    }


}
