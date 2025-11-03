<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function seatNumberView(Request $request)
    {
        $grades = Grade::all();
        $majors = Major::all();

        $gender = $request->input('gender');       // male/female یا null
        $grade_id = $request->input('grade_id');   // عدد یا null
        $major_id = $request->input('major_id');   // عدد یا null
        $students = Student::with(['grade', 'major'])
            ->whereNotNull('seat_number')
            ->when($gender, function ($query, $gender) {
                $query->where('gender', $gender);
            })
            ->when($grade_id, function ($query, $grade_id) {
                $query->where('grade_id', $grade_id);
            })
            ->when($major_id, function ($query, $major_id) {
                $query->where('major_id', $major_id);
            })
            ->get();
        return view('reports.seats.index', compact('students', 'grades', 'majors'));
    }

    public function generatePdf(Request $request)
    {
        // گرفتن فیلترها از request
        $gender = $request->input('gender');       // male/female یا null
        $grade_id = $request->input('grade_id');   // عدد یا null
        $major_id = $request->input('major_id');   // عدد یا null

        // query اصلی
        $students = Student::with(['grade', 'major'])
            ->whereNotNull('seat_number')
            ->when($gender, function ($query, $gender) {
                $query->where('gender', $gender);
            })
            ->when($grade_id, function ($query, $grade_id) {
                $query->where('grade_id', $grade_id);
            })
            ->when($major_id, function ($query, $major_id) {
                $query->where('major_id', $major_id);
            })
            ->get();

        foreach ($students as $student) {
            if ($student->photo) {
                $realPath = storage_path('app/private/students/' . basename($student->photo));

                if (file_exists($realPath)) {
                    $student->photo_path = 'file:///' . str_replace('\\', '/', $realPath);
                } else {
                    $student->photo_path = public_path('download.jpg');
                }
            } else {
                $student->photo_path = public_path('download.jpg');
            }
        }

        $pdf = Pdf::loadView('pdf.pdf', compact('students'));
        return $pdf->stream();
    }
}
