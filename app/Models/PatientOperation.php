<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientOperation extends Model
{
    use HasFactory;

    protected $table = 'patient_operation';

    protected $fillable = [
        'patient_id',
        'doctor_operation_id',
        'price',
        'paid',
        'expense',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorOperation()
    {
        return $this->belongsTo(DoctorOperation::class);
    }

    public function createLedgerEntry()
    {
        Ledger::create([
            'patient_id' => $this->patient_id,
            'debit' => $this->paid,
            'credit' => $this->price,
            'description' => 'Operation with Dr. ' . $this->doctorOperation->doctor->name,
        ]);
        $operation_remainns = $this->price - $this->expense;
        Ledger::create([
            'doctor_id' => $this->doctorOperation->doctor_id,
            'debit' => $this->doctorOperation->doctor->operation_sharing / 100 * $operation_remainns,
            'credit' => 0,
            'description' => 'Operation sharing from patient ' . $this->patient->name,
        ]);
        Ledger::create([
            'account' => 'revenue',
            'debit' => (100 - $this->doctorOperation->doctor->operation_sharing) / 100 * $operation_remainns,
            'credit' => 0,
            'description' => 'Operation sharing from patient ' . $this->patient->name,
        ]);
        Ledger::create([
            'account' => 'expense',
            'debit' => $this->expense,
            'credit' => 0,
            'description' => 'Operation expense for patient ' . $this->patient->name,
        ]);
    }
}
