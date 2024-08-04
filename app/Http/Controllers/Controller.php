<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Ledger;
use App\Models\OTAttendant;
use App\Models\Patient;
use App\Models\PatientAppointment;
use Illuminate\Database\Eloquent\Builder;
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
        $previous_balance = 0;
        if (!$ledger) {
            $ledger = Ledger::where("doctor_id", $id)->get();
        } else {
            $previous_balance =  Ledger::where("doctor_id", $id)->where("id", "<=", $ledger->id)->sum('debit') - Ledger::where("doctor_id", $id)->where("id", "<=", $ledger->id)->sum('credit');
            $ledger = Ledger::where("doctor_id", $id)->where("id", ">", $ledger->id)->get();
        }

        $doctor = Doctor::where("id", $id)->get()->first();
        return view('print_doctor_report', compact('ledger', 'doctor' , 'previous_balance'));
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

    public function print_ledger($account_type , $account_id , $date_from , $date_to){
        $query = Ledger::with(["doctor" , "patient" , "employee" , "otAttendant" , "anesthesiologist"])->get();
        $account_type = $account_type != "null" ? $account_type : null;
        $account_id = $account_id != "null" ? $account_id : null;
        $date_from = $date_from != "null" ? $date_from : null;
        $date_to = $date_to != "null" ? $date_to : null;
        $previous_balance = 0;
     
        if ($account_type ) {
            
            if ($account_type == 'patient') {
                $query =  $query->where('patient_id', $account_id);
            }
            if ($account_type == 'employee') {
                $query =  $query->where('employee_id', $account_id);
            }
            if ($account_type == 'doctor') {
                $query = $query->where('doctor_id', $account_id);
            }
            if ($account_type == 'system') {
                $query = $query->where('account', $account_id);
            }
            if ($account_type == 'ot attendant') {
                $query = $query->where('ot_attendant_id', $account_id);
            }
            if ($account_type == 'anesthesiologist') {
                $query = $query->where('anesthesiologist_id', $account_id);
            }
        }
        if($date_from){
          
            $previous_balance =  $query->where('created_at', '<', $date_from)->sum('debit') - $query->where('created_at', '<', $date_from)->sum('credit');
            $query = $query->where('created_at', '>=', $date_from);
        }
        if ($date_to)
        {
            $query = $query->where('created_at', '<=', $date_to);
        }
      
   
       
        return view('print_ledger', compact('query' , "previous_balance"));
    }
    public function summary_print($summary_print , $date_from , $date_to){
        if($summary_print == "overall"){
            $revenue = Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "revenue")->sum("debit") - Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "revenue")->sum("credit");
            $expense = Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "expense")->sum("debit") - Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "expense")->sum("credit");
            $ot_expense = Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "ot expense")->sum("debit") - Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "ot expense")->sum("credit");
            $treatment_cost = Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "treatment cost")->sum("debit") - Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("account" , "treatment cost")->sum("credit");
            $doctor_charges = Ledger::whereBetween("created_at" , [$date_from , $date_to])->whereNotNull("doctor_id" )->sum("debit");
            $doctor_paid = Ledger::whereBetween("created_at" , [$date_from , $date_to])->whereNotNull("doctor_id" )->sum("credit");
            $cash_in_hand = Ledger::where("created_at" , "<" , $date_to)->sum("debit") - Ledger::where("created_at" , "<" , $date_to)->sum("credit");
            $data = [
                "Revenue" => $revenue,
                "Expense" => $expense,
                "OT Expense" => $ot_expense,
                "Treatment Cost" => $treatment_cost,
                "Doctor Charges" => $doctor_charges,
                "Paid to Doctor" => $doctor_paid,
                "Cash in Hand" => $cash_in_hand,
            ];
            return view("summary_print" , compact("data"));
        } else if ($summary_print == "doctor") {
            $doctor = Doctor::get();
            $data = [];
            foreach($doctor as $row){
                $doctor_charges = Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("doctor_id" , $row->id)->sum("debit");
                $doctor_paid = Ledger::whereBetween("created_at" , [$date_from , $date_to])->where("doctor_id" , $row->id)->sum("credit");
                $doctor_patients = PatientAppointment::whereBetween("created_at" , [$date_from , $date_to])->where("doctor_id" , $row->id)->count("id");

                $data[$row->name ." Charges"] = $doctor_charges;
                $data[$row->name ." Paid"] = $doctor_paid;
                $data[$row->name ." No of patients"] = $doctor_paid;
            }
            return view("summary_print" , compact("data"));
        }
    }
}
