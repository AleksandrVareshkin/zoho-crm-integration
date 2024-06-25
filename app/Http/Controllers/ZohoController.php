<?php

namespace App\Http\Controllers;

use App\Service\ZohoService;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Account;

class ZohoController extends Controller
{
    public function showForm()
    {
        return view('layouts.app');
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

        [$zohoDealId, $zohoAccountId] = $zohoService->handleZohoResponse($validatedRequest['form_data']);

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
