<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorAddress extends Model
{
    use HasFactory;

    const TYPE_BASIC = 'basic';
    const TYPE_CORRESPONDENCE = 'correspondence';

    protected $fillable = [
        'contractor_id',
        'type',
        'street',
        'postcode',
        'country',
        'city',
    ];
}
