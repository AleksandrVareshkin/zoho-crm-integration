<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Deal;
use App\Models\Account;

class ZohoController extends Controller
{
    public function showForm()
    {
        return view('zoho.form');
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'deal_name' => 'required|string',
            'deal_stage' => 'required|string',
            'account_name' => 'required|string',
            'account_website' => 'required|url',
            'account_phone' => 'required|string',
        ]);

        $client = new Client(
            [
                'verify' => false
            ]
        );
        $accessToken = $this->getAccessToken();

        $dealResponse = $client->post('https://www.zohoapis.eu/crm/v2/Deals', [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            ],
            'json' => [
                'data' => [
                    [
                        'Deal_Name' => $validated['deal_name'],
                        'Stage' => $validated['deal_stage'],
                    ]
                ]
            ]
        ]);

        $dealData = json_decode($dealResponse->getBody(), true);

        //dd($dealData);
        $zohoDealId = $dealData['data'][0]['details']['id'];

        $accountResponse = $client->post('https://www.zohoapis.eu/crm/v2/Accounts', [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            ],
            'json' => [
                'data' => [
                    [
                        'Account_Name' => $validated['account_name'],
                        'Website' => $validated['account_website'],
                        'Phone' => $validated['account_phone'],
                    ]
                ]
            ]
        ]);

        $accountData = json_decode($accountResponse->getBody(), true);

        $zohoAccountId = $accountData['data'][0]['details']['id'];

        Deal::create([
            'zoho_deal_id' => $zohoDealId,
            'deal_name' => $validated['deal_name'],
            'deal_stage' => $validated['deal_stage'],
        ]);

        Account::create([
            'zoho_account_id' => $zohoAccountId,
            'account_name' => $validated['account_name'],
            'account_website' => $validated['account_website'],
            'account_phone' => $validated['account_phone'],
        ]);

        return back()->with('success', 'Deal and Account created successfully!');
    }

    private function getAccessToken()
    {
//        $client = new Client();
        $client = new Client([
            'verify' => false
        ]);
        $response = $client->post('https://accounts.zoho.eu/oauth/v2/token', [
            'form_params' => [
                'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
                'client_id' => env('ZOHO_CLIENT_ID'),
                'client_secret' => env('ZOHO_CLIENT_SECRET'),
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }
}
