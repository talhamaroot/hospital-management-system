<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BiometricEmployee extends Model
{
    use HasFactory;
    protected $table = 'biometric_employee';
    protected $fillable = ['biometric_id', 'name'];

    public function employee() : HasOne
    {
        return $this->hasOne(Employee::class, 'biometric_id');
    }
}
