<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['payment_type', 'student_product_id', 'date', 'amount', 'voucher_number', 'payment_card_id', 'receipt_image'];


    public function paymentCard()
    {
        return $this->belongsTo(PaymentCard::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
