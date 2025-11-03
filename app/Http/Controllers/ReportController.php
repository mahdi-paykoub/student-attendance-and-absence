<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class ReportController extends Controller
{
    public function seatNumberView()
    {
        $students = Student::with(['grade', 'major', 'school', 'products'])
            ->whereNotNull('seat_number')
            ->get();
        return view('reports.seats.index', compact('students'));
    }

    public function generatePdf()
    {
        $students = Student::with(['grade', 'major'])->whereNotNull('seat_number')->get();
        
        foreach ($students as $student) {
            if ($student->photo) {
                $realPath = storage_path('app/private/students/' . basename($student->photo));

                if (file_exists($realPath)) {
                    $student->photo_path = 'file:///' . str_replace('\\', '/', $realPath);
                } else {
                    $student->photo_path = null;
                }
            } else {
                $student->photo_path = null;
            }
        }

        $pdf = Pdf::loadView('pdf.pdf', compact('students'));
        return $pdf->stream();
    }
}
