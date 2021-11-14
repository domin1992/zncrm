<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Integrations\IfirmaIntegration;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Services\ContractorService;
use App\Jobs\RefreshContractor;
use Rule;

class ContractorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contractors = Contractor::paginate(50);

        return view('contractors.index', [
            'contractors' => $contractors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contractors.create');
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
            'name' => 'required|string|min:1|max:150',
            'name2' => 'string|max:150|nullable',
            'identity' => 'required|string|max:15|unique:contractors,identity',
            'eu_prefix' => 'string|max:2|nullable',
            'vat_number' => 'string|max:13|nullable',
            'email' => 'required|string|max:65',
            'phone_number' => 'string|max:32|nullable',
            'street' => 'required|string|max:65',
            'postcode' => 'required|string|min:1|max:65',
            'country' => 'required|string|max:70',
            'city' => 'required|string|min:1|max:65',
        ]);

        $contractor = Contractor::create([
            'name' => $request->name,
            'name2' => $request->name2,
            'identity' => $request->identity,
            'eu_prefix' => $request->eu_prefix,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'phisical_person' => $request->phisical_person == 'on',
            'supplier' => $request->supplier == 'on',
            'receiver' => $request->receiver == 'on',
        ]);

        ContractorAddress::create([
            'contractor_id' => $contractor->id,
            'type' => ContractorAddress::TYPE_BASIC,
            'street' => $request->street,
            'postcode' => $request->postcode,
            'country' => $request->country,
            'city' => $request->city,
        ]);

        return redirect()->route('contractors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contractor = Contractor::findOrFail();

        if(!$contractor->canBeEdited()) return redirect()->route('contractors.index');

        return view('contractors.edit', [
            'contractor' => $contractor,
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
        $contractor = Contractor::findOrFail();

        $this->validate($request, [
            'name' => 'required|string|min:1|max:150',
            'name2' => 'string|max:150|nullable',
            'identity' => ['required', 'string', 'max:15', Rule::unique('contractors', 'identity')->ignore($contractor->id)],
            'eu_prefix' => 'string|max:2|nullable',
            'vat_number' => 'string|max:13|nullable',
            'email' => 'required|string|max:65',
            'phone_number' => 'string|max:32|nullable',
            'street' => 'required|string|max:65',
            'postcode' => 'required|string|min:1|max:65',
            'country' => 'required|string|max:70',
            'city' => 'required|string|min:1|max:65',
        ]);

        $contractor->update([
            'name' => $request->name,
            'name2' => $request->name2,
            'identity' => $request->identity,
            'eu_prefix' => $request->eu_prefix,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'phisical_person' => $request->phisical_person == 'on',
            'supplier' => $request->supplier == 'on',
            'receiver' => $request->receiver == 'on',
        ]);

        $contractorAddress = $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_BASIC)->first();

        $contractorAddress->update([
            'street' => $request->street,
            'postcode' => $request->postcode,
            'country' => $request->country,
            'city' => $request->city,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 
    }

    public function ajaxRefresh($id)
    {
        RefreshContractor::dispatch($id);

        return response()->json([
            'message' => 'Job dispatched',
        ]);
    }
}
