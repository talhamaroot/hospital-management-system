<?php

use App\Http\Controllers\Controller;
use App\Livewire\Form;


\Illuminate\Support\Facades\Route::get('form', Form::class);


Route::get("/patient_recepiet/{id}" , [Controller::class, 'print'])->name("print");
Route::get("/patient_discharge/{id}" , [Controller::class, 'discharge_print'])->name("discharge_print");
Route::get("/print_doctor_report/{id}" , [Controller::class, 'print_doctor_report'])->name("print_doctor_report");
Route::get("/employee_ledger_report/{id}" , [Controller::class, 'employee_ledger_report'])->name("employee_ledger_report");
Route::get("/attendant_ledger_report/{id}" , [Controller::class, 'attendant_ledger_report'])->name("attendant_ledger_report");
Route::get("/anesthesiologist_ledger_report/{id}" , [Controller::class, 'anesthesiologist_ledger_report'])->name("anesthesiologist_ledger_report");
Route::get("/print_ledger/{account_type}/{account_id}/{date_from}/{date_to}", [Controller::class, 'print_ledger'])->name("print_ledger");
