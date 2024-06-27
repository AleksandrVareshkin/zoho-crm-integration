<?php

namespace App\Http\Controllers;

use App\Service\ZohoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Account;
use App\Models\ZohoUser;
use Illuminate\Support\Facades\Cache;

class ZohoController extends Controller
{
    public function showForm(ZohoService $zohoService)
    {
        $clientId = Cache::get('client_id');
        $refreshToken = $zohoService->getStoredRefreshToken($clientId);
        if (!$refreshToken) {
            return redirect('/auth-form');
        } else {
            return view('layouts.app');
        }
    }

    public function authCallback(Request $request, ZohoService $zohoService)
    {
        $data = $request->all();
        $code = $data['code'];
        $clientId = Cache::get('client_id');
        $user = ZohoUser::where('client_id', $clientId)->first();
        $client_secret = $user->client_secret;
        $refreshToken = $zohoService->getRefreshToken($code, $clientId, $client_secret);
        $updRefreshResult = $user->update(['refresh_token' => $refreshToken]);
        if ($updRefreshResult) {
            $accessToken = $zohoService->getAccessToken($refreshToken, $clientId, $client_secret);
            $user->update(['access_token' => $accessToken, 'access_token_expires_at' => now()->addHours(1)->timestamp]);
        }

        return redirect('/form');
    }

    public function authSubmit(Request $request, ZohoService $zohoService)
    {
        $data = $request->all();
        $clientId = $data['form_data']['client_id'];
        Cache::put('client_id', $clientId, now()->addMinute('15'));
        $clientSecret = $data['form_data']['client_secret'];
        $url = $zohoService->getAuthUrl($clientId);

        ZohoUser::updateOrCreate(
            [
                'client_id' => $clientId,
            ],
            ['client_id' => $clientId,
                'client_secret' => $clientSecret
            ]
        );
        return $url;
    }

    public function submitForm(Request $request, ZohoService $zohoService)
    {
        $validatedRequest = $request->validate([
            'form_data.deal_name' => 'required|string|max:255',
            'form_data.deal_stage' => 'required|string|max:255',
            'form_data.account_name' => 'required|string|max:255',
            'form_data.account_website' => 'required|url',
            'form_data.account_phone' => 'required|numeric',
        ], $this->customMessages());

        $formData = $validatedRequest['form_data'];

        $payloadAccount = [
            [
                'Account_Name' => $formData['account_name'],
                'Website' => $formData['account_website'],
                'Phone' => $formData['account_phone'],
            ]
        ];

        $zohoAccountId = $zohoService->createRecord($payloadAccount, 'Accounts');

        $payloadDeal = [
            [
                'Account_Name' => $formData['account_name'],
                'Deal_Name' => $formData['deal_name'],
                'Stage' => $formData['deal_stage'],
                'account_id' => $zohoAccountId,
            ]
        ];

        $zohoDealId = $zohoService->createRecord($payloadDeal, 'Deals');

        $account = Account::create([
            'zoho_account_id' => $zohoAccountId,
            'account_name' => $formData['account_name'],
            'account_website' => $formData['account_website'],
            'account_phone' => $formData['account_phone'],
        ]);

        Deal::create([
            'zoho_deal_id' => $zohoDealId,
            'deal_name' => $formData['deal_name'],
            'deal_stage' => $formData['deal_stage'],
            'account_id' => $account->id,
        ]);

        return response()->json(['status' => 'success']);
    }

    private function customMessages()
    {
        return [
            'form_data.deal_name.required' => 'The Deal Name field is required.',
            'form_data.deal_stage.required' => 'The Deal Stage field is required.',
            'form_data.account_name.required' => 'The Account Name field is required.',
            'form_data.account_website.required' => 'The Account Website field is required.',
            'form_data.account_phone.required' => 'The Account Phone field is required.',
            'form_data.account_phone.numeric' => 'The Account Phone field must be a number.',
            'form_data.account_website.url' => 'The Account Website must be a valid URL.',
        ];
    }
}
