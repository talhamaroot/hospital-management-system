<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Ledger;
use App\Models\OTAttendant;
use App\Models\Patient;
use App\Models\PatientAppointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;


    public function print($id)
    {
        $record = PatientAppointment::where("id", $id)->with(["patient", "doctor"])->get()->first();
        return view('print_receipt', compact('record'));
    }

    public function discharge_print($id)
    {
        $record = Patient::where("id", $id)->get()->first();
        return view('print_discharge', compact('record'));
    }

    public function print_doctor_report($id)
    {
        // get the entries from ledger where the id is greater then the last entry with discription Paid to Doctor
        $ledger = Ledger::where("doctor_id", $id)->where("description", "Paid to Doctor")->get()->last();
        if (!$ledger) {
            $ledger = Ledger::where("doctor_id", $id)->get();
        } else {
            $ledger = Ledger::where("doctor_id", $id)->where("id", ">", $ledger->id)->get();
        }

        $doctor = Doctor::where("id", $id)->get()->first();
        return view('print_doctor_report', compact('ledger', 'doctor'));
    }


    public function employee_ledger_report($id)
    {
        // get the entries from ledger where the id is greater then the last entry with discription Paid to Doctor
        $ledger = Ledger::where("employee_id", $id)->get();


        $employee = Employee::where("id", $id)->get()->first();
        return view('print_employee_report', compact('ledger', 'employee'));
    }


    public function attendant_ledger_report($id)
    {
        // get the entries from ledger where the id is greater then the last entry with discription Paid to Doctor
        $ledger = Ledger::where("ot_attendant_id", $id)->get();

        $ot_attendant = OTAttendant::where("id", $id)->get()->first();
        return view('print_attendant_report', compact('ledger', 'ot_attendant'));
    }

    public function anesthesiologist_ledger_report($id)
    {
        // get the entries from ledger where the id is greater then the last entry with discription Paid to Doctor
        $ledger = Ledger::where("anesthesiologist_id", $id)->get();

        $ot_attendant = OTAttendant::where("id", $id)->get()->first();
        return view('print_attendant_report', compact('ledger', 'ot_attendant'));
    }
}
