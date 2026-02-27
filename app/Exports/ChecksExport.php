<?php

namespace App\Exports;

use App\Models\Check;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Stmt\ElseIf_;

class ChecksExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return ['دانش آموز', 'تاریخ', 'مبلغ', 'سریال', 'شناسه صیاد', 'نام صاحب چک', 'وضعیت وصول'];
    }


    public function map($row): array
    {
        return [
            // ترکیب نام و نام خانوادگی برای دانش‌آموز
            $row->student->first_name . ' ' . $row->student->last_name,

            // // تاریخ با فرمت فارسی
            Jalalian::fromDateTime($row->date)->format('Y/m/d'),

            // // مبلغ با جداکننده هزارگان
            number_format($row->amount) . ' تومان',

            $row->serial, // سریال

            $row->sayad_code, // شناسه صیاد

            $row->owner_name, // نام صاحب چک

            // // وضعیت وصول شرطی
            $row->is_cleared ? 'وصول شده' :'وصول نشده' ,
          
        ];
    }

   
}
