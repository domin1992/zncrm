<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    const CYCLE_MONTHLY = 'monthly';
    const CYCLE_ANNUALY = 'annualy';

    protected $fillable = [
        'contractor_id',
        'subscription_type_id',
        'subscription_package_id',
        'comments',
        'date_start',
        'custom_price_tax_excl',
        'cycle',
        'canceled_at',
    ];

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id', 'id');
    }

    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionType::class, 'subscription_type_id', 'id');
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id', 'id');
    }

    public function shouldRenew()
    {
        $now = Carbon::now();
        $now->hour = 23;
        $now->minute = 59;
        $now->second = 59;

        $subscriptionHistories = $this->subscriptionHistories()->orderBy('created_at', 'asc')->get();
        $lastSubscriptionHistory = $subscriptionHistories->last();
        $lastSubscriptionHistoryCreatedAt = Carbon::parse($lastSubscriptionHistory->created_at);

        switch($this->cycle){
            case Subscription::CYCLE_MONTHLY:
                $now->subMonth();
                break;
            case Subscription::CYCLE_ANNUALY:
                $now->subYear();
                break;
        }

        if($lastSubscriptionHistoryCreatedAt->lt($now) && $lastSubscriptionHistory->status == SubscriptionHistory::STATUS_PAID){
            return true;
        }

        return false;
    }

    public function getPriceTaxExcl()
    {
        if(null != $this->custom_price_tax_excl) return $this->custom_price_tax_excl;

        $subscriptionPackage = $this->subscriptionPackage()->first();
        return $subscriptionPackage->price_tax_excl;
    }

    public function getInvoicePostionName()
    {
        $subscriptionType = $this->subscriptionType()->first();
        $invoicePositionName = $subscriptionType->invoice_position_name;

        if($subscriptionType->slug == 'domain'){
            $invoicePositionName = str_replace('{domain}', $this->comments, $invoicePositionName);
        }

        return $invoicePositionName;
    }
}
