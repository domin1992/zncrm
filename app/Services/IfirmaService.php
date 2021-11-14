<?php

namespace App\Services;

use App\Integrations\IfirmaIntegration;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Carbon\Carbon;

class IfirmaService
{
    public function proFormInvoice(Contractor $contractor, Subscription $subscription, SubscriptionHistory $subscriptionHistory)
    {
        $now = Carbon::now();

        $ifirmaIntegration = new IfirmaIntegration;
        $contractorSearchResults = $ifirmaIntegration->findContractor((null != $contractor->vat_number ? $contractor->vat_number : $contractor->identity));

        $subscriptionHistoryCreatedAt = Carbon::parse($subscriptionHistory->created_at);
        $subscriptionType = $subscription->subscriptionType()->first();
        $subscriptionPackage = $subscription->subscriptionPackage()->first();

        $params = [
            'LiczOd' => 'NET',
            'TypFakturyKrajowej' => 'SPRZ',
            'DataWystawienia' => $now->format('Y-m-d'),
            'TerminPlatnosci' => $subscriptionHistory->payment_date,
            'SposobZaplaty' => 'PRZ',
            'NazwaSeriiNumeracji' => 'Zencore pro forma',
            'RodzajPodpisuOdbiorcy' => 'BWO',
            'Uwagi' => 'W tytule przelewu prosimy podać numer faktury',
            'WidocznyNumerGios' => false,
            'WidocznyNumerBdo' => false,
            'Numer' => null,
            'NumerZamowienia' => $subscription->reference.'_'.$subscriptionHistoryCreatedAt->format('Y_m_d'),
            'Pozycje' => [
                [
                    'StawkaVat' => 0.23,
                    'Ilosc' => 1,
                    'CenaJednostkowa' => (float)number_format($subscriptionHistory->price_tax_excl, 2, '.', ''),
                    'NazwaPelna' => $subscription->getInvoicePostionName(),
                    'Jednostka' => 'szt.',
                    'TypStawkiVat' => 'PRC',
                ]
            ],
        ];

        if(null != $contractorSearchResults && 1 == count($contractorSearchResults)){
            if(null != $contractor->vat_number){
                $params['NIPKontrahenta'] = $contractor->vat_number;
            }
            else{
                $params['IdentyfikatorKontrahenta'] = $contractor->identity;
            }
        }
        else{
            $params['Kontrahent'] = [
                'Nazwa' => $contractor->name,
                'Nazwa2' => $contractor->name2,
                'Identyfikator' => $contractor->identity,
                'NIP' => $contractor->vat_number,
                'Ulica' => $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_BASIC)->first()->street,
                'KodPocztowy' => $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_BASIC)->first()->postcode,
                'Kraj' => $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_BASIC)->first()->country,
                'Miejscowosc' => $contractor->contractorAddress()->where('type', ContractorAddress::TYPE_BASIC)->first()->city,
                'Email' => $contractor->email,
                'Telefon' => $contractor->phone_number,
                'OsobaFizyczna' => $contractor->phisical_person,
                'JestOdbiorca' => $contractor->receiver,
                'JestDostawca' => $contractor->supplier,
            ];
        }

        return $ifirmaIntegration->proFormInvoice($params);
    }
}