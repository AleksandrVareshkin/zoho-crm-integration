<?php

namespace App\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ZohoService
{
    private $client;

    public function __construct()
    {
        $accessToken = Cache::remember('access_token', now()->addMinutes(60), fn() => $this->getAccessToken());

        $this->client = new Client(
            [
                'base_uri' => 'https://www.zohoapis.eu/crm/v2/',
                'verify' => false,
                'headers' => ['Authorization' => 'Zoho-oauthtoken ' . $accessToken],
            ]);
    }

    public function handleZohoResponse($validatedResponse)
    {
        $accountResponse = $this->client->post('Accounts', [
            'json' => [
                'data' => [
                    [
                        'Account_Name' => $validatedResponse['account_name'],
                        'Website' => $validatedResponse['account_website'],
                        'Phone' => $validatedResponse['account_phone'],
                    ]
                ]
            ]
        ]);

        $accountData = json_decode($accountResponse->getBody(), true);
        $zohoAccountId = $accountData['data'][0]['details']['id'];

        $dealResponse = $this->client->post('Deals', [
            'json' => [
                'data' => [
                    [
                        'Deal_Name' => $validatedResponse['deal_name'],
                        'Stage' => $validatedResponse['deal_stage'],
                        'Account_Name' => $zohoAccountId,
                    ]
                ]
            ]
        ]);

        $dealData = json_decode($dealResponse->getBody(), true);
        $zohoDealId = $dealData['data'][0]['details']['id'];

        return [$zohoDealId, $zohoAccountId];
    }

    private function getAccessToken()
    {
        $client = new Client(
            [
                'verify' => false
            ]
        );
        $response = $client->post('https://accounts.zoho.eu/oauth/v2/token', [
            'form_params' => [
                'refresh_token' => config('zoho.refresh_token'),
                'client_id' => config('zoho.client_id'),
                'client_secret' => config('zoho.client_secret'),
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }
}
