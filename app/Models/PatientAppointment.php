<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAppointment extends Model
{
    use HasFactory;

    protected $table = 'patient_appointments';

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {

            $model->createLedgerEntry();
        });


    }
    public function createLedgerEntry()
    {
        Ledger::create([
            'patient_id' => $this->patient_id,

            'debit' => $this->paid,
            'credit' => $this->price,
            'description' => 'Appointment with Dr. ' . $this->doctor->name,

        ]);
        Ledger::create([
            "doctor_id" => $this->doctor_id,
            "debit" => $this->doctor->outdoor_sharing / 100 * $this->price,
            "credit" => 0,
            "description" => "Outdoor sharing from patient " . $this->patient->name,
        ]);
        Ledger::create([
            "account" => "revenue",
            "debit" => (100 - $this->doctor->outdoor_sharing) / 100 * $this->price,
            "credit" => 0,
            "description" => "Outdoor sharing from patient " . $this->patient->name,
        ]);
    }

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'price',
        'paid',
        'temperature',
        'bp',
        'weight',
    ];

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor() : BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

}
