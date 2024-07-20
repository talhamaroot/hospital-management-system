<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ledger extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;


    protected $table = 'ledger';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'employee_id',
        'debit',
        'credit',
        'description',
        'ot_attendant_id',
        "account"

    ];

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor() : BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }


    public function otAttendant() : BelongsTo
    {
        return $this->belongsTo(OTAttendant::class);
    }


    public function anesthesiologist() : BelongsTo
    {
        return $this->belongsTo(Anesthesiologist::class);
    }
}
