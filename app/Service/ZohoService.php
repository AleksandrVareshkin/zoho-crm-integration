<?php

namespace App\Service;

use App\Models\ZohoUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

class ZohoService
{
    private $client;
    private string $clientId;
    private string $code;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
//        $this->clientId = config('zoho.client_id');
//        $this->clientSecret = config('zoho.client_secret');
        $this->redirectUri = config('zoho.redirect_uri');

//        $accessToken = Cache::remember('access_token', now()->addMinutes(60), function () {
//            $refreshToken = $this->getStoredRefreshToken();
//            if (!$refreshToken) {
//                $refreshToken = $this->getRefreshToken();
//                $this->storeRefreshToken($refreshToken);
//            }
//            return $this->getAccessToken($refreshToken);
//        });

//        $this->client = new Client([
//            'base_uri' => 'https://www.zohoapis.eu/crm/v2/',
//            'verify' => false,
//            'headers' => ['Authorization' => 'Zoho-oauthtoken ' . $accessToken],
//        ]);
    }

//    public function handleZohoResponse($validatedResponse)
//    {
//        $accountResponse = $this->client->post('Accounts', [
//            'json' => [
//                'data' => [
//                    [
//                        'Account_Name' => $validatedResponse['account_name'],
//                        'Website' => $validatedResponse['account_website'],
//                        'Phone' => $validatedResponse['account_phone'],
//                    ]
//                ]
//            ]
//        ]);
//
//        $accountData = json_decode($accountResponse->getBody(), true);
//        $zohoAccountId = $accountData['data'][0]['details']['id'];
//
//        $dealResponse = $this->client->post('Deals', [
//            'json' => [
//                'data' => [
//                    [
//                        'Deal_Name' => $validatedResponse['deal_name'],
//                        'Stage' => $validatedResponse['deal_stage'],
//                        'Account_Name' => $zohoAccountId,
//                    ]
//                ]
//            ]
//        ]);
//
//        $dealData = json_decode($dealResponse->getBody(), true);
//        $zohoDealId = $dealData['data'][0]['details']['id'];
//
//        return [$zohoDealId, $zohoAccountId];
//    }

    /**
     * @param array $payload
     * @param string $moduleName
     * @return string|null
     * @throws GuzzleException
     */
    public function createRecord(array $payload, string $moduleName) : ?string
    {
        $recordResponse = $this->client->post($moduleName, [
            'json' => [
                'data' => $payload
            ]
        ]);

        $recordData = json_decode($recordResponse->getBody(), true);
        if (isset($recordData['data'][0])) {
            return $recordData['data'][0]['details']['id'];
        }else{
            return null;
        }
    }

    public function getStoredRefreshToken()
    {
        $tokenRecord = ZohoUser::latest()->first();
        return $tokenRecord ? $tokenRecord->refresh_token : null;
    }

    private function storeRefreshToken($refreshToken)
    {
        ZohoUser::create(['refresh_token' => $refreshToken]);
    }

    private function getRefreshToken()
    {
        $client = new Client(['verify' => false]);
        $response = $client->post('https://accounts.zoho.eu/oauth/v2/token', [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $this->code,
                'grant_type' => 'authorization_code',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        dd($data);
        return $data['refresh_token'];
    }

    private function getAccessToken($refreshToken)
    {
        $client = new Client(['verify' => false]);
        $response = $client->post('https://accounts.zoho.eu/oauth/v2/token', [
            'form_params' => [
                'refresh_token' => $refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

//    public function getCode()
//    {
//        $codeClient = new Client();
//        $response = $codeClient->get('https://accounts.zoho.eu/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=' . $this->clientId . '&response_type=code&access_type=offline&redirect_uri=' . $this->redirectUri);
//
//        var_dump($response->getBody());
//        $locationHeader = $response->getHeader('Location');
//
//        if (!empty($locationHeader)) {
//            $this->code = $locationHeader[0];
//        } else {
//            throw new \Exception('Location header not found in response');
//        }
//
//        return $this->code;
//    }

    public function getAuthUrl(
        string $clientId,
    )
    {
        return 'https://accounts.zoho.eu/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=' . $clientId . '&response_type=code&access_type=offline&redirect_uri=' . $this->redirectUri;
    }
}
