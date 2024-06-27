<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Models\ZohoUser;
use Illuminate\Support\Facades\Log;

class ZohoService
{
    private $client;
    private string | null $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct($clientId = null)
    {
        $this->redirectUri = config('zoho.redirect_uri');
        $this->clientId = $clientId;
    }

    public function createClient(){
        $accessToken = $this->getValidAccessToken();
        $this->client = new Client([
            'base_uri' => 'https://www.zohoapis.eu/crm/v2/',
            'verify' => false,
            'headers' => ['Authorization' => 'Zoho-oauthtoken ' . $accessToken],
        ]);
    }

    /**
     * @param array $payload
     * @param string $moduleName
     * @return string|null
     * @throws GuzzleException
     */
    public function createRecord(array $payload, string $moduleName): ?string
    {
        $this->createClient();
        $recordResponse = $this->client->post($moduleName, [
            'json' => [
                'data' => $payload
            ]
        ]);

        $recordData = json_decode($recordResponse->getBody(), true);
        if (isset($recordData['data'][0])) {
            return $recordData['data'][0]['details']['id'];
        } else {
            return null;
        }
    }

    public function getValidAccessToken(){
        if($this->clientId){
            $user = ZohoUser::where(['client_id' => $this->clientId])->first();
        }
        else{
            $user = ZohoUser::first();
        }
        if($user->access_token_expires_at > now()->timestamp){
            return $user->access_token;
        }
        else{
            $refreshToken= $user->refresh_token;
            $clientId = $user->client_id;
            $clientSecret = $user->client_secret;
            $accessToken = $this->getAccessToken($refreshToken, $clientId, $clientSecret);
            $user->update(['access_token_expires_at' => now()->timestamp + 3600, 'access_token' => $accessToken]);
            return $accessToken;
        }

    }
    public function getStoredRefreshToken(string|null $clientId = null)
    {
        if ($clientId) {
            $user = ZohoUser::where(['client_id' => $clientId])->first();
        } else {
            $user = ZohoUser::first();
        }
        if(!$user){
            return null;
        }
        $this->clientId = $user->client_id;
        $this->clientSecret = $user->client_secret;
        return $user->refresh_token ?? null;
    }

    public function getRefreshToken($code, $clientId, $clientSecret)
    {
        $client = new Client(['verify' => false]);
        $response = $client->post('https://accounts.zoho.eu/oauth/v2/token', [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $this->redirectUri,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return $data['refresh_token'];
    }

    public function getAccessToken($refreshToken, $clientId, $clientSecret)
    {
        $client = new Client(['verify' => false]);
        $response = $client->post('https://accounts.zoho.eu/oauth/v2/token', [
            'form_params' => [
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    public function getAuthUrl(
        string $clientId,
    )
    {
        return 'https://accounts.zoho.eu/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=' . $clientId . '&response_type=code&access_type=offline&redirect_uri=' . $this->redirectUri;
    }
}
