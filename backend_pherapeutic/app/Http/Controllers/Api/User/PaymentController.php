<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\TherapistType;
use App\Models\Appointments;
use Auth;
use Validator;
use Stripe;

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

  public function makePayment(Request $request){
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
      //'appointment_id' => 'required'
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

    //\Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    $charge = \Stripe\Charge::create([
      'amount' => \Config::get('services.stripe.amount'),
      'currency' => \Config::get('services.stripe.currency'),
      'source' => 'tok_visa',
      'description' => 'Payment to pherapeutic',
    ]);

        //$charge['paid']=false;
    if($charge['paid']!=true){
        $result = [
            "statusCode" => 409, 
            "message" => 'Transaction failed',

        ];
    return response()->json($result);
    }
    
    $result = [
      "statusCode" => 200, 
      "message" => 'Payment Success',
      "data" => [
        'txn_id' => $charge['balance_transaction']
      ]
    ];
    return response()->json($result);
  }

}
