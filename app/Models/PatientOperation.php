<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PatientOperation extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'patient_operation';

    protected $fillable = [
        'patient_id',
        'doctor_operation_id',
        'price',
        'paid',
        'expense',
        'status',
        "ot_attendant_id",
        "referred_by"
    ];

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorOperation() : BelongsTo
    {
        return $this->belongsTo(DoctorOperation::class);
    }

    public function otAttendant() : BelongsTo
    {
        return $this->belongsTo(OTAttendant::class);
    }

    public function anesthesiologist() : BelongsTo
    {
        return $this->belongsTo(Anesthesiologist::class);
    }

    public function createLedgerEntry($ot_attendant_fee , $anesthesiologist_fee)
    {
        Ledger::create([
            'patient_id' => $this->patient_id,
            'debit' => $this->paid,
            'credit' => $this->price,
            'description' => 'Operation with Dr. ' . $this->doctorOperation->doctor->name,
        ]);
        $operation_remainns = $this->price - $this->expense - $ot_attendant_fee;
        if($anesthesiologist_fee){
            $operation_remainns = $operation_remainns - $anesthesiologist_fee;
        }
        if ($this->doctorOperation->fixed_price > 0){
            Ledger::create([
                'doctor_id' => $this->doctorOperation->doctor_id,
                'debit' => $this->doctorOperation->fixed_price,
                'credit' => 0,
                'description' => 'Operation sharing from patient ' . $this->patient->name,
            ]);
            Ledger::create([
                'account' => 'revenue',
                'debit' => $operation_remainns - $this->doctorOperation->fixed_price,
                'credit' => 0,
                'description' => 'Operation sharing from patient ' . $this->patient->name,
            ]);
        }
        else if($this->referred_by == "hospital"){
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
        } else if ($this->referred_by == "doctor"){
            Ledger::create([
                'doctor_id' => $this->doctorOperation->doctor_id,
                'debit' => $this->doctorOperation->doctor->referred_operation_sharing / 100 * $operation_remainns,
                'credit' => 0,
                'description' => 'Operation sharing from patient ' . $this->patient->name,
            ]);
            Ledger::create([
                'account' => 'revenue',
                'debit' => (100 - $this->doctorOperation->doctor->referred_operation_sharing) / 100 * $operation_remainns,
                'credit' => 0,
                'description' => 'Operation sharing from patient ' . $this->patient->name,
            ]);
        }
        Ledger::create([
            'account' => 'ot expense',
            'debit' => $this->expense,
            'credit' => 0,
            'description' => 'Operation expense for patient ' . $this->patient->name,
        ]);
        Ledger::create([
            "ot_attendant_id" => $this->ot_attendant_id,
            'debit' => $ot_attendant_fee,
            'credit' => 0,
            'description' => 'Operation Fee for patient ' . $this->patient->name,
        ]);

        if($anesthesiologist_fee){
            Ledger::create([
                "anesthesiologist_id" => $this->anesthesiologist_id,
                'debit' => $anesthesiologist_fee,
                'credit' => 0,
                'description' => 'Operation Fee for patient ' . $this->patient->name,
            ]);
        }
    }
}
