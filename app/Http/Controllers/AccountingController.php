<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function registerPercantageView()
    {
        $students = Student::all();
        return view('accounting.registerPercantage' ,compact('students'));
    }
}
