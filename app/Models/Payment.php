<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['student_product_id', 'date', 'time', 'amount', 'voucher_number', 'payment_card_id', 'receipt_image'];


    public function productStudent()
    {
        return $this->belongsTo(ProductStudent::class);
    }

    public function paymentCard()
    {
        return $this->belongsTo(PaymentCard::class);
    }
}
