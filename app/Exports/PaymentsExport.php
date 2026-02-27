<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Morilog\Jalali\Jalalian;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        // اضافه کردن روابط مورد نیاز
        return $this->query->with(['student']);
    }

    public function headings(): array
    {
        return [

            'دانش آموز',
            'کدملی',
            'مبلغ',
            'نحوه واریز',
            'تاریخ واریز',

        ];
    }

    public function map($pay): array
    {
        return [

            $pay->student->first_name . ' ' . $pay->student->last_name,
            $pay->student->national_code,
            number_format($pay->amount),
            $pay->payment_type == 'cash' ? 'نقدی': ' پیش‌پرداخت قسط' ,
            $pay->date ? Jalalian::fromDateTime($pay->date)->format('Y/m/d') : '-',

        ];
    }
}
