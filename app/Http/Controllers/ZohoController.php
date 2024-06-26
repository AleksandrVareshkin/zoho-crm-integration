<?php

namespace App\Http\Controllers;

use App\Service\ZohoService;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Account;

class ZohoController extends Controller
{
    public function showForm(ZohoService $zohoService)
    {
        $refreshToken = $zohoService->getStoredRefreshToken();

        if (!$refreshToken) {
            return redirect('/auth-form');
        } else {
            return redirect('/form');
        }
    }

    public function authForm()
    {
        return redirect('auth-form');
    }


    public function authSubmit(Request $request, ZohoService $zohoService)
    {
        $clientId = $request->input('client_id');
        $url = $zohoService->getAuthUrl($clientId);
        return redirect($url);
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
