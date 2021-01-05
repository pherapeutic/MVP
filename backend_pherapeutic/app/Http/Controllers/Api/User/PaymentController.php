<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\TherapistType;
use App\Models\Appointments;
use App\Models\CallLogs;
use App\Models\PaymentDetails;
use App\Models\Settings;
use App\Models\User;
use Auth;
use Validator;
use Stripe;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function stripeToken(Request $request)
    {

      //Create Stripe Token
      $token = \Stripe\Token::create(array(
        "card" => array(
          "number"    => $request->input('card_number'),
          "exp_month" => $request->input('exp_month'),
          "exp_year"  => $request->input('exp_year'),
          "cvc"       => $request->input('cvc'),
          "name"      => $request->input('name')
      )));

      return $token->id;
    }
    
  public function addUserCard(Request $request)  { 
    $user = $this->request->user();
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result); 
    }
    
    $validator = Validator::make($request->all(), [ 
                'card_token' => 'required'
              ]);
  
    if ($validator->fails()) { 
      $errors = $validator->errors()->all();
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => $errors[0]
        );
      return response()->json($result );            
    }

    $input = $request->all();

    try{
      if(!$user->stripe_id){
        $customer = \Stripe\Customer::create(['email' => $user->email]);
        $user->stripe_id = $customer['id'];
      } else {
        \Stripe\Customer::update(
          $user->stripe_id,
          ['email' => $user->email]
        );
      }

      // tok_visa is the token which will generate in client side
      $stripeCard = \Stripe\Customer::createSource(
                        $user->stripe_id,
                        ['source' => $input['card_token']]
                      );
    } catch(\Exception $ex){
      $result = array(
        "statusCode" => 401,
        "message" => $ex->getMessage()
      );
      return response()->json($result );
    }

    if(isset($stripeCard['id']) && $stripeCard['id'] != ''){
      $userCardArr = [
        'user_id' => $user->id,
        'card_token' => $stripeCard['id']
      ];

      //$userCardObj = UserCard::create($userCardArr);
      $userCardObj = $user->save();

      if($userCardObj){
        $result = array(
          "statusCode" => 200,  // $this-> successStatus
          "message" => "success"
        );
  
        return response()->json($result);
      } else {
        $result = array(
          "statusCode" => 500,
          "message" => "Unable to add card. Please try again later."  
        );
        return response()->json($result); 
      }

    } else {
      $result = array(
        "statusCode" => 500,
        "message" => "Unable to add card. Please try again later."  
      );
      return response()->json($result); 
    }
  }

  public function getUserCards(){
    $user = $this->request->user();
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result ); 
    }
    
    if(!$user->stripe_id){
      $result = array(
        "statusCode" => 200,  // $this-> successStatus
        "message" => 'No card found of the user.'
      );
      return response()->json($result ); 
    }

    try{
      $userCards = \Stripe\Customer::allSources(
        $user->stripe_id,
        ['object' => 'card']
      );
    } catch(\Exception $ex){
      $result = array(
        "statusCode" => 401,
        "message" => $ex->getMessage()
      );
      return response()->json($result );
    }

    $userCards = $userCards->data;

    $result = array(
      "statusCode" => 200,  // $this-> successStatus
      "message" => "success",
      "data" => $userCards
    );

    return response()->json($result);
  }

  public function deleteUserCard(Request $request){
    $user = $this->request->user();
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result ); 
    }

    $error = "";
    if(!$request->has('card_id') || $request->input('card_id') == ''){
      $error = "card id is mandatory";
    }
    if($error != "") {
      $result = array(
        "statusCode" => 401,  // $this-> successStatus
        "message" => $error 
      );
      return response()->json($result ); 
    }

    if(!$user->stripe_id){
      $result = array(
        "statusCode" => 200,  // $this-> successStatus
        "message" => 'No card found of the user.'
      );
      return response()->json($result ); 
    }

    try{
      $hasDeleted = \Stripe\Customer::deleteSource(
        $user->stripe_id,
        $request->input('card_id')
      );
    } catch(\Exception $ex){
      $result = array(
        "statusCode" => 401,
        "message" => $ex->getMessage()
      );
      return response()->json($result );
    }

    // $userCard = UserCard::where('user_id', $user->id)->where('id', $request->input('card_id'))->first();
    if($hasDeleted){
      $result = array(
        "statusCode" => 200,
        "message" => "success"
      );
    } else {
      $result = array(
        "statusCode" => 500,
        "message" => "Unable to delete card."
      );
    }
    return response()->json($result);
  }

  public function createDefaultCard(Request $request){
    $user = $this->request->user();
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result ); 
    }

    $validator = Validator::make($request->all(), [ 
            'card_id' => 'required',
    ]);

    if ($validator->fails()) { 
      $errors = $validator->errors()->all();
        $result = array(
          "statusCode" => 401,
          "message" => $errors[0]
        );
      return response()->json($result);            
    }

    if($request->has('card_id') && $request->input('card_id') != ''){
      if(!$user->stripe_id){
        $result = array(
          "statusCode" => 200,  // $this-> successStatus
          "message" => 'No card found of the user.'
        );
        return response()->json($result ); 
      }

      try{
        \Stripe\Customer::update(
          $user->stripe_id, 
          [ 'default_source' => $request->input('card_id') ]
        );

        //$user->default_payment_method = 'card';
        $hasUpdated = $user->save();

      } catch(\Exception $ex){
        $result = array(
          "statusCode" => 401,
          "message" => $ex->getMessage()
        );
        return response()->json($result );
      }
    } 
    // else if($request->has('payment_method_type') && $request->input('payment_method_type') == 'apple_pay'){
    //   $user->default_payment_method = 'apple_pay';
    //   $hasUpdated = $user->save();
    // } 
    else {
      $result = array(
        "statusCode" => 401,
        "message" => 'Invalid Input'
      );
      return response()->json($result);  
    }

    if($hasUpdated){
      $result = array(
        "statusCode" => 200,
        "message" => "success"
      );
    } else {
      $result = array(
        "statusCode" => 500,
        "message" => "Unable to delete card."
      );
    }
    return response()->json($result);
  }

  public function amountHoldBeforeCall(Request $request, Appointments $appointment){
    $user = $this->request->user();
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result ); 
    }

    $validator = Validator::make($request->all(), [ 
      'card_id' => 'required',
      'appointment_id' => 'required',
    ]);

    if ($validator->fails()) { 
      $errors = $validator->errors()->all();
      $result = array(
        "statusCode" => 401,  // $this-> successStatus
        "message" => $errors[0]
      );
      return response()->json($result );            
    }

    $input = $request->all();
    //Get appointment object
    $appointmentObj = $appointment->getAppointmentById($input['appointment_id']);
    if(!$appointmentObj){
        $result = array(
          "statusCode" => 404,  // $this-> successStatus
          "message" => 'Appointment not found.'
        );
      return response()->json($result ); 
    }
    //Get appoint therapist for this appointmet
    $appointTherapist = $appointmentObj->therapist;
    if(!$appointTherapist){
        $result = array(
          "statusCode" => 404,  // $this-> successStatus
          "message" => 'Therapist not found.'
        );
      return response()->json($result );      
    }
    //Check therapist is connect with stripe or not
    if(!$appointTherapist->stripe_connect_id){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'Call could not be connect.'
        );
      return response()->json($result );
    }

    try{

      $charge = \Stripe\Charge::create([
        'amount' => \Config::get('services.stripe.amount')*100,
        'currency' => \Config::get('services.stripe.currency'),
        'description' => 'Payment to pherapeutic for appointment id '.$input['appointment_id'].'',
        'customer' => $user->stripe_id,
        'source' => $input['card_id'],
        'capture' => false,
      ]);

          //$charge['paid']=false;
      if($charge['paid']!=true){
          $result = [
              "statusCode" => 409, 
              "message" => 'Transaction failed, '.$charge['failure_message'].', failed code'.$charge['failure_code'].'',
          ];
      return response()->json($result);
      }


      //if($charge['paid']){
        $appointmentPaymentArr = [
            'appointment_id' => $appointmentObj->id,
            'charge_id' => $charge['id'],
            'txn_id' => $charge['balance_transaction'],
            'amount' => number_format($charge['amount']/100,2),
            'is_captured' => '0',
            'card_id' => $charge['payment_method']
        ];

        PaymentDetails::create($appointmentPaymentArr);
        //appointment status update
        $appointmentObj->status = '2';
        $appointmentObj->save();

      
        $result = [
          "statusCode" => 200, 
          "message" => 'Payment amount hold in you card.',
          "data" => [
            'charge_id' => $charge['id']
          ]
        ];
        return response()->json($result);        
      //}

    } catch(\Exception $ex){
        $result = array(
            "statusCode" => 401,
            "message" => $ex->getMessage()
        );
        return response()->json($result );
    }


  }

  public function makePayment(Request $request, Appointments $appointment){
    $user = $this->request->user();
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result ); 
    }

    $validator = Validator::make($request->all(), [ 
      'appointment_id' => 'required',
      'charge_id' => 'required'
    ]);

    if ($validator->fails()) { 
      $errors = $validator->errors()->all();
      $result = array(
        "statusCode" => 401,  // $this-> successStatus
        "message" => $errors[0]
      );
      return response()->json($result );            
    }

    $input = $request->all();

    //Get appointment object
    $appointmentObj = $appointment->getAppointmentById($input['appointment_id']);
    if(!$appointmentObj){
        $result = array(
          "statusCode" => 404,  // $this-> successStatus
          "message" => 'Appointment not found.'
        );
      return response()->json($result ); 
    }
    //Get appoint therapist for this appointmet
    $appointTherapist = $appointmentObj->therapist;
    if(!$appointTherapist){
        $result = array(
          "statusCode" => 404,  // $this-> successStatus
          "message" => 'Therapist not found.'
        );
      return response()->json($result );      
    }
    
    $paymentDetailsObj = PaymentDetails::where('appointment_id', $input['appointment_id'])
                      ->where('charge_id', $input['charge_id'])->where('is_captured', '0')->first();

    if(!$paymentDetailsObj){
        $result = array(
          "statusCode" => 404,  // $this-> successStatus
          "message" => 'No hold payment found.'
        );
      return response()->json($result );       
    }

    try{
      //charge done for admin
      $charge = \Stripe\Charge::retrieve($paymentDetailsObj->charge_id);
      $charge->capture();

      if($charge['paid']!=true){
          $result = [
              "statusCode" => 409, 
              "message" => 'Transaction failed, '.$charge['failure_message'].', failed code'.$charge['failure_code'].'',

          ];
      return response()->json($result);
      }

      $settingObj = Settings::first();
      $applicationCharge = $settingObj->app_charge;
      $amount = \Config::get('services.stripe.amount');
      $amountPercentage = (($amount*$applicationCharge)/100);
      $therapistAmount = $amount-$amountPercentage;

      // Create a Transfer to a connected therapist account
      $transfer = \Stripe\Transfer::create([
        'amount' => $therapistAmount*100,
        'currency' => \Config::get('services.stripe.currency'),
        'source_transaction' => $paymentDetailsObj->charge_id,
        'destination' => $appointTherapist->stripe_connect_id,
        'transfer_group' => 'Transfer done for appointment id #'.$appointmentObj->id.', transfer to account:'.$appointTherapist->stripe_connect_id.'',
      ]);
      //store data in payment details model
      if($transfer){
        $paymentDetailsObj->transfer_id = $transfer['id'];
        $paymentDetailsObj->transfer_amount = number_format($transfer['amount']/100,2);
        $paymentDetailsObj->transfer_to_account = $transfer['destination'];
      }
      $paymentDetailsObj->txn_id = $charge['balance_transaction'];
      $paymentDetailsObj->is_captured = '1';
      $paymentDetailsObj->save();

      //appointment status update
      $appointmentObj->status = '3';
      $appointmentObj->ended_at = Carbon::now();
      $appointmentObj->save();      

      $result = [
        "statusCode" => 200, 
        "message" => 'Payment Success',
        "data" => [
          'txn_id' => $charge['balance_transaction']
        ]
      ];
      return response()->json($result);

    } catch(\Exception $ex){
        $result = array(
            "statusCode" => 401,
            "message" => $ex->getMessage()
        );
        return response()->json($result );
    }

  }

  public function createCall(Request $request, CallLogs $callLogs){
    $user = $this->request->user();
    $callerId = $callLogs->generateCallerId();    
    
    if($user->role != '0'){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'User is not authenticated.'
        );
      return response()->json($result ); 
    }

    $validator = Validator::make($request->all(), [ 
      'card_id' => 'required',
      'therapist_id' => 'required',
    ]);

    if ($validator->fails()) { 
      $errors = $validator->errors()->all();
      $result = array(
        "statusCode" => 401,  // $this-> successStatus
        "message" => $errors[0]
      );
      return response()->json($result );            
    }

    $input = $request->all();

    //check on going call
    $lastCallObj = $callLogs->getNotEndedCall($user->id);
    if($lastCallObj){
      //make payment
      $paymentDetailsObj = PaymentDetails::where('call_logs_id', $lastCallObj->id)
                            ->where('is_captured', '0')->first();
      $appointTherapist = $lastCallObj->therapist;

      if($lastCallObj->duration > 10){

        try{
          //charge done for admin
          $dynamicAmount = $lastCallObj->duration*100;
          $charge = \Stripe\Charge::retrieve($paymentDetailsObj->charge_id);
          $charge->capture([
            'amount' => $dynamicAmount,
          ]);

          if($charge['paid']!=true){
              $result = [
                  "statusCode" => 409, 
                  "message" => 'Transaction failed, '.$charge['failure_message'].', failed code'.$charge['failure_code'].'',

              ];
          return response()->json($result);
          }

          $settingObj = Settings::first();
          $applicationCharge = $settingObj->app_charge;
          //$amount = \Config::get('services.stripe.amount');
          $amount = $lastCallObj->duration;
          $amountPercentage = (($amount*$applicationCharge)/100);
          $therapistAmount = $amount-$amountPercentage;

          // Create a Transfer to a connected therapist account
          $transfer = \Stripe\Transfer::create([
            'amount' => $therapistAmount*100,
            'currency' => \Config::get('services.stripe.currency'),
            'source_transaction' => $paymentDetailsObj->charge_id,
            'destination' => $appointTherapist->stripe_connect_id,
            'transfer_group' => 'Transfer done for caller id #'.$lastCallObj->id.', transfer to account:'.$appointTherapist->stripe_connect_id.'',
          ]);
          //store data in payment details model
          if($transfer){
            $paymentDetailsObj->transfer_id = $transfer['id'];
            $paymentDetailsObj->transfer_amount = number_format($transfer['amount']/100,2);
            $paymentDetailsObj->transfer_to_account = $transfer['destination'];
          }
          $paymentDetailsObj->txn_id = $charge['balance_transaction'];
          $paymentDetailsObj->refund_amount = number_format($charge['amount_refunded']/100,2);
          $paymentDetailsObj->refund_id = $charge['refunds']['data'][0]['id'];

          if($charge['amount'] <= $charge['amount_captured']){
            $paymentDetailsObj->is_captured = '1';            
          }else{
            $paymentDetailsObj->is_captured = '3';
          }
          $paymentDetailsObj->save();

          //call log status update
          $lastCallObj->ended_at = Carbon::now();           
          $lastCallObj->call_status = '2';           
          $lastCallObj->payment_status = '2';           
          $hasUpdate = $lastCallObj->save();      

          // $result = [
          //   "statusCode" => 200, 
          //   "message" => 'Payment Success',
          //   "data" => [
          //     'txn_id' => $charge['balance_transaction']
          //   ]
          // ];
          // return response()->json($result);

        } catch(\Exception $ex){
            $result = array(
                "statusCode" => 401,
                "message" => $ex->getMessage()
            );
            return response()->json($result );
        }

      }else{
        //Refund all amount
        try{
          $refund = \Stripe\Refund::create(['charge' => $paymentDetailsObj->charge_id]);
          if($refund){
            //update payment details
            if($paymentDetailsObj){
              $paymentDetailsObj->is_captured = '2';
              $paymentDetailsObj->refund_id = $refund['id'];
              $paymentDetailsObj->refund_amount = number_format($refund['amount']/100,2);
              $paymentDetailsObj->save();
            }

            //call log status update
            $lastCallObj->ended_at = Carbon::now();
            $lastCallObj->call_status = '2';
            $lastCallObj->payment_status = '3';
            $hasUpdate = $lastCallObj->save();            
          }
        } catch(\Exception $ex){
            $result = array(
                "statusCode" => 401,
                "message" => $ex->getMessage()
            );
            return response()->json($result );
        }
      }
    }

    //Get appoint therapist for this appointmet
    $callTherapist = User::find($input['therapist_id']);

    if(!$callTherapist){
        $result = array(
          "statusCode" => 404,  // $this-> successStatus
          "message" => 'Therapist not found.'
        );
      return response()->json($result );      
    }
    //Check therapist is connect with stripe or not
    if(!$callTherapist->stripe_connect_id){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'Call could not be connect.'
        );
      return response()->json($result );
    }

    //Check therapist is Online or not
    if(!$callTherapist->online_status){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'Call could not be connected, Therapist is off-line.'
        );
      return response()->json($result );
    }
    //create call
    $callCreateArr = [
        'caller_id' => $callerId,
        'user_id' => $user->id,
        'therapist_id' => $input['therapist_id']
    ];
    $hasCreatedCall = CallLogs::create($callCreateArr);
    if(!$hasCreatedCall){
        $result = array(
          "statusCode" => 401,  // $this-> successStatus
          "message" => 'Call could not be connected, Internal server error.'
        );
      return response()->json($result );      
    }

    try{

      $charge = \Stripe\Charge::create([
        'amount' => \Config::get('services.stripe.amount')*100,
        'currency' => \Config::get('services.stripe.currency'),
        'description' => 'Payment to pherapeutic for caller id '.$callerId.'',
        'customer' => $user->stripe_id,
        'source' => $input['card_id'],
        'capture' => false,
      ]);

          //$charge['paid']=false;
      if($charge['paid']!=true){
          $result = [
              "statusCode" => 409, 
              "message" => 'Transaction failed, '.$charge['failure_message'].', failed code'.$charge['failure_code'].'',
          ];
      return response()->json($result);
      }


      //if($charge['paid']){
        $callPaymentArr = [
            'call_logs_id' => $hasCreatedCall->id,
            'charge_id' => $charge['id'],
            'txn_id' => $charge['balance_transaction'],
            'amount' => number_format($charge['amount']/100,2),
            'is_captured' => '0',
            'card_id' => $charge['payment_method']
        ];

        PaymentDetails::create($callPaymentArr);
      
        $result = [
          "statusCode" => 200, 
          "message" => 'Payment amount hold in you card.',
          "data" => [
            'charge_id' => $charge['id'],
            'caller_id' => $hasCreatedCall->caller_id
          ]
        ];
        return response()->json($result);        
      //}

    } catch(\Exception $ex){
        $result = array(
            "statusCode" => 401,
            "message" => $ex->getMessage()
        );
        return response()->json($result );
    }


  }

}
