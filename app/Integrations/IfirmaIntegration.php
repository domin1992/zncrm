<?php

namespace App\Integrations;

use GuzzleHttp\Client;
use Log;
use Storage;

class IfirmaIntegration
{
    protected $user;
    protected $invoiceApiKey;
    protected $subscriberApiKey;
    protected $client;

    const BASE_URI = 'https://www.ifirma.pl/iapi/';

    const API_KEY_INVOICE_NAME = 'faktura';
    const API_KEY_SUBSCRIBER_NAME = 'abonent';

    public function __construct()
    {
        $this->user = env('IFIRMA_USER');
        $this->invoiceApiKey = env('IFIRMA_INVOICE_API_KEY');
        $this->subscriberApiKey = env('IFIRMA_SUBSCRIBER_API_KEY');
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 10,
        ]);
    }

    protected function hmac($key, $data)
    {
		$blocksize = 64;
		$hashfunc = 'sha1';
		if (strlen($key) > $blocksize)
			$key = pack('H*', $hashfunc($key));
		$key = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
		return bin2hex($hmac);
	}

    protected function hexToStr($hex)
    {
        $string = '';
		for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
			$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $string;
    }

    protected function request($type, $endpoint, $params = '', $apiKey, $apiKeyName)
    {
        if($type == 'GET'){
            $requestContent = '';
        }
        elseif(is_array($params)){
            $requestContent = json_encode($params);
        }
        else{
            $requestContent = $params;
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-type' => 'application/json; charset=UTF-8',
            'User-Agent' => 'Zncrm/1.0.0',
            'Authentication' => 'IAPIS user='.$this->user.', hmac-sha1='.$this->hmac($this->hexToStr($apiKey), sprintf(
                "%s%s%s%s",
                self::BASE_URI.$endpoint,
                $this->user,
                $apiKeyName,
                $requestContent
            )),
        ];

        $requestParams = [
            'headers' => $headers,
            'body' => $requestContent,
        ];

        return $this->client->request($type, $endpoint, $requestParams);
    }

    public function checkLimit()
    {
        try{
            $response = $this->request('GET', 'abonent/limit.json', '', $this->subscriberApiKey, self::API_KEY_SUBSCRIBER_NAME);

            return json_decode((string)$response->getBody());
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }

    public function changeAccountingMonth()
    {
        try{
            $response = $this->request('PUT', 'abonent/miesiacksiegowy.json', [
                'MiesiacKsiegowy' => 'NAST',
                'PrzeniesDaneZPoprzedniegoRoku' => false,
            ], $this->subscriberApiKey, self::API_KEY_SUBSCRIBER_NAME);

            return json_decode((string)$response->getBody());
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }

    public function proFormInvoice($invoiceParams = [])
    {
        try{
            $response = $this->request('POST', 'fakturaproformakraj.json', $invoiceParams, $this->invoiceApiKey, self::API_KEY_INVOICE_NAME);

            if(json_decode((string)$response->getBody())->response->Kod == 201 && 'Pole \'Data wystawienia\' musi być zgodna z miesiącem i rokiem księgowym' == json_decode((string)$response->getBody())->response->Informacja){
                $this->changeAccountingMonth();

                $response = $this->request('POST', 'fakturaproformakraj.json', $invoiceParams, $this->invoiceApiKey, self::API_KEY_INVOICE_NAME);
            }

            if(json_decode((string)$response->getBody())->response->Kod != 0){
                Log::debug(json_decode((string)$response->getBody())->response->Informacja);
                return null;
            }

            return $this->downloadInvoiceProForm(json_decode((string)$response->getBody())->response->Identyfikator);
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }

    public function downloadInvoiceProForm($invoiceProFormId)
    {
        try{
            $response = $this->request('GET', 'fakturaproformakraj/'.$invoiceProFormId.'.pdf', '', $this->invoiceApiKey, self::API_KEY_INVOICE_NAME);
            Storage::put('docs/pro-form/'.$invoiceProFormId.'.pdf', (string)$response->getBody());

            return [
                'invoice_pro_form_id' => $invoiceProFormId,
                'invoice_pro_form_storage_path' => 'docs/pro-form/'.$invoiceProFormId.'.pdf',
            ];
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }

    public function invoiceFromInvoiceProForm($invoiceProFormId)
    {
        try{
            $response = $this->request('GET', 'fakturaproformakraj/add/'.$invoiceProFormId.'.json', '', $this->invoiceApiKey, self::API_KEY_INVOICE_NAME);

            return $this->downloadInvoice(json_decode((string)$response->getBody())->response->Identyfikator);
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }

    public function downloadInvoice($invoiceId)
    {
        try{
            $response = $this->request('GET', 'fakturakraj/'.$invoiceId.'.pdf', '', $this->invoiceApiKey, self::API_KEY_INVOICE_NAME);
            Storage::put('docs/invoices/'.$invoiceId.'.pdf', (string)$response->getBody());

            return [
                'invoice_id' => $invoiceId,
                'invoice_storage_path' => 'docs/invoices/'.$invoiceId.'.pdf',
            ];
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }

    public function findContractor($s)
    {
        try{
            $response = $this->request('GET', 'kontrahenci/'.$s.'.json', '', $this->invoiceApiKey, self::API_KEY_INVOICE_NAME);

            if(json_decode((string)$response->getBody())->response->Kod == 0)
                return json_decode((string)$response->getBody())->response->Wynik;

            return null;
        }
        catch(\GuzzleHttp\Exception\RequestException $e){
            Log::debug(Psr7\Message::toString($e->getRequest()));
            if($e->hasResponse()){
                Log::debug(Psr7\Message::toString($e->getResponse()));
            }

            return null;
        }
    }
}