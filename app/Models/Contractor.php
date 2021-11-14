<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name2',
        'identity',
        'eu_prefix',
        'vat_number',
        'email',
        'phone_number',
        'phone_number2',
        'phisical_person',
        'agreed_for_electronic_invoice',
        'invoice_email',
        'supplier',
        'receiver',
        'abroad_address',
        'skype',
        'fax',
        'comments',
        'website',
        'bank_name',
        'bank_number',
        'selected',
        'send_invoice_on_both_emails',
    ];

    public function contractorAddress()
    {
        return $this->hasMany(ContractorAddress::class);
    }

    public function canBeEdited()
    {
        return null == Subscription::where('contractor_id', $this->id)->first();
    }
}
