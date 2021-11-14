<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Services\SubscriptionService;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = Subscription::paginate(50);

        return view('subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'contractor_id' => 'required|integer|min:1|exists:contractors,id',
            'subscription_type_id' => 'required|integer|min:1|exists:subscription_types,id',
            'subscription_package_id' => 'required|integer|min:1|exists:subscription_packages,id',
            'comments' => 'string|nullable',
            'date_start' => 'required|date_format:"Y-m-d"',
            'custom_price_tax_excl' => 'numeric|nullable',
            'cycle' => ['required', Rule::in([Subscription::CYCLE_MONTHLY, Subscription::CYCLE_ANNUALY])],
        ]);

        $subscriptionService = new SubscriptionService;
        $subscription = $subscriptionService->createSubscription($request);

        return redirect()->route('subscriptions.show', [$subscription->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription = Subscription::findOrFail();

        return view('subscriptions.show', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subscription = Subscription::findOrFail();

        return view('subscriptions.edit', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail();

        $this->validate($request, [
            'subscription_package_id' => 'required|integer|min:1|exists:subscription_packages,id',
            'comments' => 'string|nullable',
            'custom_price_tax_excl' => 'numeric|nullable',
        ]);

        $subscription->update([
            'subscription_package_id' => $request->subscription_package_id,
            'comments' => $request->comments,
            'custom_price_tax_excl' => $request->custom_price_tax_excl,
        ]);

        return redirect()->route('subscriptions.show', [$subscription->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail();
        $subscription->delete();

        return redirect()->route('subscriptions.index');
    }
}
