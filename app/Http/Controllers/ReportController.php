<?php

namespace App\Http\Controllers;

use App\Exports\ChecksExport;
use App\Exports\DepositsExport;
use App\Exports\PaymentsExport;
use App\Exports\StudentsReportExport;
use App\Models\Account;
use App\Models\Check;
use App\Models\Deposit;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Payment;
use App\Models\SmsReport;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;

class ReportController extends Controller
{


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

        $pdf = Pdf::loadView('pdf.seatReport', compact('students'));
        return $pdf->stream();
    }




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




    public function customDataView(Request $request)
    {
        $columns = $request->get('columns', []);

        if (empty($columns)) {
            $columns = ['first_name', 'last_name'];
        }

        $students = \App\Models\Student::with(['payments', 'checks', 'products'])->get();

        return view('reports.custom-students-data.index', compact('students', 'columns'));
    }



    public function generateStudentsCustomFielsPdf(Request $request)
    {
        $columns = $request->get('columns', []);

        // اگه هیچ ستونی انتخاب نشده بود
        if (empty($columns)) {
            $columns = ['first_name', 'last_name'];
        }

        // برای ستون‌های محاسباتی (مثل وضعیت پرداخت) باید روابط رو هم بارگذاری کنیم
        $students = Student::with(['payments', 'checks', 'products'])->get();

        // ارسال داده‌ها به ویو PDF
        $pdf = Pdf::loadView('pdf.fieldReport', compact('students', 'columns'));
        return $pdf->stream();
    }



    public function generateStudentsCustomFieldsExcel(Request $request)
    {
        $columns = $request->get('columns', []);

        // اگر هیچ ستونی انتخاب نشده
        if (empty($columns)) {
            $columns = ['first_name', 'last_name', 'national_code'];
        }

        // گرفتن دانش‌آموزان با روابط مورد نیاز
        $students = Student::with([
            'payments',
            'checks',
            'products',
            'grade',
            'major',
            'school',
            'advisor',
        ])->get();

        // نام فایل
        $fileName = 'students_report_' . Jalalian::now()->format('Y-m-d_H-i') . '.xlsx';

        return Excel::download(
            new StudentsReportExport($columns, $students),
            $fileName
        );
    }

    public function getDebtorStudemtsView()
    {
        $debtors = Student::with(['products', 'payments'])
            ->get()
            ->filter(function ($student) {
                return $student->debt > 0;
            });

        return view('reports.debtor-students.index', compact('debtors'));
    }


    public function getDebtorStudemtsPdf()
    {
        $debtors = Student::with(['products', 'payments'])
            ->get()
            ->filter(function ($student) {
                return $student->debt > 0;
            });
        // ارسال داده‌ها به ویو PDF
        $pdf = Pdf::loadView('pdf.deptors', compact('debtors'));
        return $pdf->stream();
    }




    public function getDepositssView(Request $request)
    {
        $accounts = Account::all();
        $account_id = $request->query('account_id');

        if ($account_id) {
            $deposits = Deposit::where('account_id', $account_id)->get();
        } else {
            // اگر چیزی انتخاب نشده بود، همه واریزی‌ها
            $deposits = Deposit::all();
        }

        return view('reports.deposits.index', compact('accounts', 'deposits', 'account_id'));
    }

    public function getDdepositsPdf(Request $request)
    {
        $account_id = $request->query('account_id');

        if ($account_id) {
            $deposits = Deposit::where('account_id', $account_id)->get();
        } else {
            // اگر چیزی انتخاب نشده بود، همه واریزی‌ها
            $deposits = Deposit::all();
        }

        // ارسال داده‌ها به ویو PDF
        $pdf = Pdf::loadView('pdf.deposits', compact('deposits'));
        return $pdf->stream();
    }

    public function getDdepositsExel(Request $request)
    {
        $account_id = $request->query('account_id');

        if ($account_id) {

            $query = Deposit::where('account_id', $account_id);
        } else {
            $query = Deposit::query();
        }


        return Excel::download(
            new DepositsExport($query),
            'checks.xlsx'
        );
    }

    public function getChecksView(Request $request)
    {
        $checks = Check::query();

        // اگر فیلتر ارسال شد
        if ($request->has('status')) {
            if ($request->status == 'cleared') {
                $checks->where('is_cleared', true);
            }
            if ($request->status == 'not_cleared') {
                $checks->where('is_cleared', false);
            }
        }

        $checks = $checks->latest()->with('student')->get();
        $totalCleared = Check::where('is_cleared', true)->sum('amount');
        $totalUnCleared = Check::where('is_cleared', false)->sum('amount');

        return view('reports.checks.index', compact('checks', 'totalCleared', 'totalUnCleared'));
    }
    public function getPaysView()
    {
        $payments = Payment::with('student')->latest()->get();

        return view('reports.student-pays.index', compact('payments'));
    }
    public function getPaysPdf()
    {
        $payments = Payment::with('student')->latest()->get();

        // ارسال داده‌ها به ویو PDF
        $pdf = Pdf::loadView('pdf.pays', compact('payments'));
        return $pdf->stream();
    }

    public function getPaysExel()
    {
        $query = Payment::with('student')->latest();

        return Excel::download(
            new PaymentsExport($query),
            'checks.xlsx'
        );
    }



    public function getChecksPdf(Request $request)
    {
        $checks = Check::query();

        // اگر فیلتر ارسال شد
        if ($request->has('status')) {
            if ($request->status == 'cleared') {
                $checks->where('is_cleared', true);
            }
            if ($request->status == 'not_cleared') {
                $checks->where('is_cleared', false);
            }
        }

        $checks = $checks->latest()->with('student')->get();

        // ارسال داده‌ها به ویو PDF
        $pdf = Pdf::loadView('pdf.checks', compact('checks'));
        return $pdf->stream();
    }

    public function getChecksExel(Request $request)
    {
        $checks = Check::query();

        // اگر فیلتر ارسال شد
        if ($request->has('status')) {
            if ($request->status == 'cleared') {
                $checks->where('is_cleared', true);
            }
            if ($request->status == 'not_cleared') {
                $checks->where('is_cleared', false);
            }
        }

        $query = $checks->latest()->with('student');
        return Excel::download(
            new ChecksExport($query),
            'checks.xlsx'
        );
    }


    public function smsReportsView(Request $request)
    {
        $query = SmsReport::query()->with(['student', 'template']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('national_code', 'like', "%{$search}%");
            });
        }

        $smsReports = $query->latest()->get();
        return view('reports.sms.sms', compact('smsReports'));
    }
}
