<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Mail\SubscriptionRenewal as SubscriptionRenewalMail;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Carbon\Carbon;
use Log;
use Mail;
use Str;

class SubscriptionService
{
    public function createSubscription(Request $request)
    {
        $now = Carbon::now();

        do{
            $reference = Str::upper(Str::random(8));
        }while(null != Subscription::where('reference', $reference)->first());

        $subscription = Subscription::create([
            'contractor_id' => $request->contractor_id,
            'subscription_type_id' => $request->subscription_type_id,
            'subscription_package_id' => $request->subscription_package_id,
            'reference' => $reference,
            'comments' => $request->comments,
            'date_start' => $request->date_start,
            'custom_price_tax_excl' => $request->custom_price_tax_excl,
            'cycle' => $request->cycle,
        ]);

        $subscriptionHistory = SubscriptionHistory::create([
            'subscription_id' => $subscription->id,
            'payment_date' => $now->addWeeks(2)->format('Y-m-d'),
            'status' => SubscriptionHistory::STATUS_WAITING_FOR_PAYMENT,
            'price_tax_excl' => $subscription->getPriceTaxExcl(),
        ]);

        $ifirmaService = new IfirmaService;
        $result = $ifirmaService->proFormInvoice($subscription->contractor()->first(), $subscription, $subscriptionHistory);

        if(null != $result){
            $subscriptionHistory->update([
                'invoice_pro_form_id' => $result['invoice_pro_form_id'],
            ]);

            Mail::to($subscription->contractor()->first()->email)->send(new SubscriptionRenewalMail($subscription->subscriptionType()->first(), $result['invoice_pro_form_storage_path']));
        }

        return $subscription;
    }

    public function renewSubscription(Subscription $subscription)
    {
        $now = Carbon::now();

        if($subscription->shouldRenew()){
            $subscriptionPackage = $subscription->subscriptionPackage()->first();

            $subscriptionHistory = SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'payment_date' => $now->addWeeks(2)->format('Y-m-d'),
                'status' => SubscriptionHistory::STATUS_WAITING_FOR_PAYMENT,
                'price_tax_excl' => $subscription->getPriceTaxExcl(),
            ]);

            $ifirmaService = new IfirmaService;
            $result = $ifirmaService->proFormInvoice($subscription->contractor()->first(), $subscription, $subscriptionHistory);

            if(null != $result){
                $subscriptionHistory->update([
                    'invoice_pro_form_id' => $result['invoice_pro_form_id'],
                ]);

                Mail::to($subscription->contractor()->first()->email)->send(new SubscriptionRenewalMail($subscription->subscriptionType()->first(), $result['invoice_pro_form_storage_path']));
            }
        }
    }
}