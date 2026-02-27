<?php
// app/Exports/StudentsReportExport.php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Morilog\Jalali\Jalalian;

class StudentsReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $columns;
    protected $students;

    public function __construct($columns, $students)
    {
        $this->columns = $columns;
        $this->students = $students;
    }

    public function collection()
    {
        // بارگذاری روابط مورد نیاز برای محاسبات
        return $this->students->load(['payments', 'checks', 'products', 'grade', 'major', 'school', 'advisor']);
    }

    public function headings(): array
    {
        $headers = [];

        foreach ($this->columns as $col) {
            if ($col === 'payment_status') {
                $headers[] = 'جمع پرداخت‌ها';
                $headers[] = 'جمع محصولات';
                $headers[] = 'بدهکاری';
            } else {
                $headers[] = $this->getColumnTitle($col);
            }
        }

        return $headers;
    }

    public function map($student): array
    {
        $row = [];

        foreach ($this->columns as $col) {
            if ($col === 'payment_status') {
                // محاسبات پرداخت مانند PDF
    

                $totalPayments = $student->payments()->where('payment_type', 'cash')->sum('amount');
                $totalPrepayments = $student->payments()->where('payment_type', 'installment')->sum('amount');
                $totalChecks = $student->checks()->where('is_cleared', true)->sum('amount'); // اضافه کردن شرط
                $totalPaid = $totalPayments + $totalPrepayments + $totalChecks;

                $totalProducts = $student->products->sum(function ($product) {
                    $taxAmount = $product->price * ($product->tax_percent / 100);
                    return $product->price + $taxAmount;
                });

                // اعمال تخفیف (اگر در accessor وجود دارد)
                $discount = $student->discounts()->first()?->amount ?? 0;
                $totalProductsAfterDiscount = max($totalProducts - $discount, 0);

                $debt = max($totalProductsAfterDiscount - $totalPaid, 0);




                $row[] = number_format($totalPaid) . ' تومان';
                $row[] = number_format($totalProducts) . ' تومان';
                $row[] = number_format($debt) . ' تومان';
            } elseif (in_array($col, ['created_at', 'birthday', 'custom_date'])) {
                // تاریخ‌ها به فارسی
                $row[] = $student->$col ? Jalalian::fromDateTime($student->$col)->format('Y/m/d - H:i') : '-';
            } elseif ($col === 'gender') {
                // جنسیت به فارسی
                $row[] = $student->gender === 'male' ? 'پسر' : ($student->gender === 'female' ? 'دختر' : '-');
            } elseif ($col === 'photo') {
                // فقط مسیر عکس
                $row[] = $student->photo ? basename($student->photo) : 'download.jpg';
            } elseif (in_array($col, ['grade_id', 'major_id', 'school_id', 'advisor_id', 'advisor_id'])) {
                // نمایش نام رابطه‌ها
                $row[] = $this->getRelationName($student, $col);
            } else {
                // فیلدهای معمولی
                $row[] = $student->$col ?? '-';
            }
        }

        return $row;
    }

    private function getColumnTitle($col): string
    {
        $titles = [
            'photo' => 'عکس',
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'gender' => 'جنسیت',
            'father_name' => 'نام پدر',
            'national_code' => 'کد ملی',
            'mobile_student' => 'موبایل دانش‌آموز',
            'grade_id' => 'پایه',
            'major_id' => 'رشته',
            'school_id' => 'مدرسه',
            'province' => 'استان',
            'city' => 'شهر',
            'consultant_id' => 'مشاور',
            'referrer_id' => 'معرف',
            'custom_date' => 'تاریخ سفارشی',
            'birthday' => 'تاریخ تولد',
            'address' => 'آدرس',
            'phone' => 'تلفن منزل',
            'mobile_father' => 'موبایل پدر',
            'mobile_mother' => 'موبایل مادر',
            'notes' => 'یادداشت',
            'seat_number' => 'شماره صندلی',
            'created_at' => 'تاریخ ایجاد',
        ];

        return $titles[$col] ?? str_replace('_', ' ', $col);
    }

    private function getRelationName($student, $relationField)
    {
        $relationMap = [
            'grade_id' => 'grade',
            'major_id' => 'major',
            'school_id' => 'school',
            'consultant_id' => 'advisor',
            'referrer_id' => 'advisor',
        ];

        if (isset($relationMap[$relationField]) && $student->{$relationMap[$relationField]}) {
            return $student->{$relationMap[$relationField]}->name ??
                $student->{$relationMap[$relationField]}->title ?? '-';
        }

        return '-';
    }
}
