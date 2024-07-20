<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attandence extends Model
{
    use HasFactory;
    protected $table = "attendance";
    protected $fillable = [
        "employee_id",
        "time_in",
        "time_out"
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
