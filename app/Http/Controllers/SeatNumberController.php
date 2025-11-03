<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Base;
use App\Models\Grade;
use App\Models\Major;
use App\Models\ProductStudent;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeatNumberController extends Controller
{
    public function index()
    {
        $students = Student::latest()->get();
        return view('seats.index', compact('students'));
    }

    public function generate()
    {
        DB::transaction(function () {
            $mandatoryExamId = Setting::where('key', 'mandatory_exam_product_id')->value('value');

            $genders = ['male', 'female'];

            foreach ($genders as $gender) {
                $seatNumber = ($gender === 'female') ? 1000 : 2000;

                $bases = Grade::orderBy('id')->get();
                foreach ($bases as $base) {
                    $majors = Major::orderBy('id')->get();

                    foreach ($majors as $major) {
                        $students = Student::where('gender', $gender)
                            ->where('grade_id', $base->id)
                            ->where('major_id', $major->id)
                            ->orderBy('id')
                            ->get();

                        foreach ($students as $s) {
                            $hasMandatory = ProductStudent::where('student_id', $s->id)
                                ->where('product_id', $mandatoryExamId)
                                ->exists();

                            if ($hasMandatory) {
                                $s->update(['seat_number' => $seatNumber++]);
                            }
                        }
                    }
                }
            }
        });

        return back()->with('success', 'شماره صندلی‌ها با موفقیت تولید شدند.');
    }
}
