<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipient;
use Illuminate\Support\Facades\Validator;
use App\Libraries\TransferWise;

class RecipientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipient = Recipient::first();
        $is_recipient = (!empty($recipient)) ? 1 : 0;
        
        $data = [
            'recipient' => $recipient,
            'is_recipient' => $is_recipient
        ];
        
        return view('recipients',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $recipient = Recipient::first();
        $is_recipient = (!empty($recipient)) ? 1 : 0;
        if ($is_recipient == 1) {
            return redirect(route('recipients'))->with('errors', "Recipient already exists")->withInput();
        }
        
        $rules = [
            'recipient_type' => 'required|in:1,2',
            'ac_holder_name' => 'required',
        ];

        $validationErrorMessages = [];

        $validator = Validator::make($request->all(), $rules, $validationErrorMessages);

        if ($validator->fails()) {
            return redirect(route('recipients'))->with('errors', $validator->messages())->withInput();
        }
        
        $recipient_type = $request->get('recipient_type');
        $recipient_type = ($recipient_type == 1) ? 'PRIVATE' : 'BUSINESS';
        $account_holder_name = $request->get('ac_holder_name');
        
        $tw_profile = cache('transferwise_profile');
        $profile_id = $tw_profile[0]['id']; 
        
        $recipient_data = [
            'currency' => "INR",
            'type' => 'indian',
            'profile' => $profile_id,
            'accountHolderName' => $account_holder_name,
            'details' => [
                'legalType' => $recipient_type,
                'ifscCode' => "YESB0236041", 
                'accountNumber' => "678911234567891"
            ]
        ];
        
        $tw = new TransferWise;
        $response = $tw->postRequest('accounts',$recipient_data);
        
        $recipient_data = [
            'recipient_id' => $response['id'],
            'profile' => $response['profile'],
            'account_holder_name' => $response['accountHolderName'],
            'currency' => $response['currency'],
            'country' => $response['country'],
            'type' => $response['type'],
            'details' => $response['details'],
            'user' => $response['user'],
            'active' => $response['active'],
        ];
        
        Recipient::create($recipient_data);
        
        return redirect(route('recipients'))->with('success', "Recipient Added Successfully");
    }

}
