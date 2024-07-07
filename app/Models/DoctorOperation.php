<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorOperation extends Model
{
    use HasFactory;

    protected $table = 'doctor_operation';

    public function doctor() : BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function operation() : BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

}
