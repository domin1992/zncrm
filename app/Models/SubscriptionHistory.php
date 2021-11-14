<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    use HasFactory;

    const STATUS_WAITING_FOR_PAYMENT = 'waiting_for_payment';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'subscription_id',
        'payment_date',
        'status',
        'price_tax_excl',
        'ifirma_invoice_pro_form_id',
        'ifirma_invoice_id',
    ];
}
