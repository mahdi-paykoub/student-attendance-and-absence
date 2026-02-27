<?php
// app/Exports/DepositsExport.php

namespace App\Exports;

use App\Models\Deposit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Morilog\Jalali\Jalalian;

class DepositsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;
    
    public function __construct($query)
    {
        $this->query = $query;
    }
    
    public function query()
    {
        // اضافه کردن روابط مورد نیاز
        return $this->query->with(['account']);
    }
    
    public function headings(): array
    {
        return [
           
            'عنوان',
            'حساب',
            'مبلغ (تومان)',
            'تاریخ',
           
        ];
    }
    
    public function map($deposit): array
    {
        return [
           
            $deposit->title ?? 'بدون عنوان',
            $deposit->account->name ?? 'نامشخص',
            number_format($deposit->amount),
            $deposit->paid_at ? Jalalian::fromDateTime($deposit->paid_at)->format('Y/m/d') : '-',
          
        ];
    }
}