<?php

namespace App\Http\Controllers;

use App\Models\Attandence;
use App\Models\BiometricEmployee;
use App\Models\Employee;
use Illuminate\Http\Request;

class RestApiController extends Controller
{
    function postBiometricData(Request $request){


        $attandences = $request->input('attandences');

        foreach ($attandences as $attandence){
            $employee = Employee::where('biometric_id', $attandence['uid'])->first();
            if($attandence['type'] == 0){
                Attandence::create([
                    "employee_id" => $employee->id,
                    "time_in" => $attandence['timestamp']
                ]);
            } else {
                $lastAttendance = Attandence::where('employee_id', $employee->id)->latest()->first();
                $lastAttendance->update([
                    "time_out" => $attandence['timestamp']
                ]);
            }
        }

        return response()->json([
            "message" => "Data has been saved successfully"
        ]);
    }

    function postBiometricDataUser(Request $request){
        $accounts = $request->input('accounts');
        foreach ($accounts as $account){
            $checkAccount = BiometricEmployee::where('biometric_id', $account['uid'])->first();
            if(!$checkAccount){
                BiometricEmployee::create([
                    'biometric_id' => $account['uid'],
                    'name' => $account['name']
                ]);
            }
        }
    }
}
