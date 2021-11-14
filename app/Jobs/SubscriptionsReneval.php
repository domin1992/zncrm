<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Log;

class SubscriptionsReneval implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        $subscriptionService = new SubscriptionService;

        $subscriptions = Subscription::where('cycle', Subscription::CYCLE_ANNUALY)
            ->where('canceled_at', null)
            ->where('date_start', 'like', '%'.$now->format('-m-d'))
            ->get();

        $subscriptions = Subscription::where('cycle', Subscription::CYCLE_MONTHLY)
            ->where('canceled_at', null)
            ->where('date_start', 'like', '%'.$now->format('-d'))
            ->get();

        foreach($subscriptions as $subscription){
            $subscriptionService->renewSubscription($subscription);
        }
    }
}
