<?php

namespace App\Services;

use App\Integrations\IfirmaIntegration;
use App\Models\Contractor;
use App\Models\ContractorAddress;

class ContractorService
{
    public function refreshContractor($contractorId)
    {
        $contractor = Contractor::findOrFail($id);

        $ifirmaIntegration = new IfirmaIntegration;
        $result = $ifirmaIntegration->findContractor($contractor->vat_number ? $contractor->vat_number : $contractor->identity);

        if(null != $result && 1 == count($result)){
            $contractorFromIfirma = $result[1];
            $contractor->update([
                'name' => $contractorFromIfirma->Nazwa,
                'name2' => $contractorFromIfirma->Nazwa2,
                'identity' => $contractorFromIfirma->Identyfikator,
                'eu_prefix' => $contractorFromIfirma->PrefiksUE,
                'vat_number' => $contractorFromIfirma->NIP,
                'email' => $contractorFromIfirma->Email,
                'phone_number' => $contractorFromIfirma->Telefon,
                'phone_number2' => $contractorFromIfirma->DrugiTelefon,
                'phisical_person' => $contractorFromIfirma->OsobaFizyczna,
                'agreed_for_electronic_invoice' => $contractorFromIfirma->ZgodaNaEfaktury,
                'invoice_email' => $contractorFromIfirma->EmailDlaFaktury,
                'supplier' => $contractorFromIfirma->JestDostawca,
                'receiver' => $contractorFromIfirma->JestOdbiorca,
                'abroad_address' => $contractorFromIfirma->AdresZagraniczny,
                'skype' => $contractorFromIfirma->Skype,
                'fax' => $contractorFromIfirma->Faks,
                'comments' => $contractorFromIfirma->Uwagi,
                'website' => $contractorFromIfirma->Www,
                'bank_name' => $contractorFromIfirma->NazwaBanku,
                'bank_number' => $contractorFromIfirma->NumerKonta,
                'selected' => $contractorFromIfirma->Wybrany,
                'send_invoice_on_both_emails' => $contractorFromIfirma->WyslijNaObaEmaile,
            ]);

            $contractorAddress = $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_BASIC)->first();
            $contractorAddress->update([
                'street' => $contractorFromIfirma->Ulica,
                'postcode' => $contractorFromIfirma->KodPocztowy,
                'country' => $contractorFromIfirma->Kraj,
                'city' => $contractorFromIfirma->Miejscowosc,
            ]);

            if(
                '' != $contractorFromIfirma->AdresKorespondencyjnyUlica
                && '' != $contractorFromIfirma->AdresKorespondencyjnyKodPocztowy
                && '' != $contractorFromIfirma->AdresKorespondencyjnyKraj
                && '' != $contractorFromIfirma->AdresKorespondencyjnyMiejscowosc
            ){
                $contractorAddress = $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_CORRESPONDENCE)->first();
                if(null != $contractorAddress){
                    $contractorAddress->update([
                        'street' => $contractorFromIfirma->AdresKorespondencyjnyUlica,
                        'postcode' => $contractorFromIfirma->AdresKorespondencyjnyKodPocztowy,
                        'country' => $contractorFromIfirma->AdresKorespondencyjnyKraj,
                        'city' => $contractorFromIfirma->AdresKorespondencyjnyMiejscowosc,
                    ]);
                }
                else{
                    ContractorAddress::create([
                        'contractor_id' => $contractor->id,
                        'type' => ContractorAddress::TYPE_CORRESPONDENCE,
                        'street' => $contractorFromIfirma->AdresKorespondencyjnyUlica,
                        'postcode' => $contractorFromIfirma->AdresKorespondencyjnyKodPocztowy,
                        'country' => $contractorFromIfirma->AdresKorespondencyjnyKraj,
                        'city' => $contractorFromIfirma->AdresKorespondencyjnyMiejscowosc,
                    ]);
                }
            }
        }
    }
}