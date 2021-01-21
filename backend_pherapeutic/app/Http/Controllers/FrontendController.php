<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Session;
use Stripe;

class FrontendController extends Controller
{
    
    public function connectwithstrip(Request $request)
    {  
        $user = User::find($request->input('user_id'));
        if(!$user){
            $error = 'No user found!';
            $noUser = 1;
            return view('connectwithstrip',compact('error', 'noUser'));
        }
        if($user->role != User::THERAPIST_ROLE){
            $error = 'Unauthorized user!';
            $noUser = 1;
            return view('connectwithstrip',compact('error', 'noUser'));        	
        }
        Auth::loginUsingId($user->id);

        return view('connectwithstrip',compact('user'));
       
    }

    public function sendcurltostrip(Request $request)
    { 
    
    $headers = array();
    $headers[] = "Content-Type: application/json";    
    $data = [
       'client_secret' => \Config::get('services.stripe.secret'),
       //'client_secret' => 'sk_test_51Hc2GiHjeqWbGW6kopJKI80U2kZPr8WjuUkoGZfcu4b7IunarDQwXeCSwTG5cgBlSpZZIMMPj9dXNmNlm9ejYu9300EcvuYwrI',
       'code' => $request->code,
       'grant_type' => 'authorization_code',            
     ];
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://connect.stripe.com/oauth/token"); 
    // SSL important
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $output = curl_exec($ch);
    curl_close($ch);
    
    $response = json_decode($output);
    $user = Auth::user(); 
    $error = 'Something went wrong!';
    $noUser = 1;

    if(!empty($response->stripe_user_id))  
    { 
      //echo '<script>location.href="http://127.0.0.1:8000/connectwithstrip?stripe_account_id='.$response->stripe_user_id.'"</script>'; 

      $input = array();
      $user->stripe_connect_id = $response->stripe_user_id;     
      $hasSave = $user->save();
      $message = 'Account is connected!';
      Auth::logout();
      //dd($input);
      
    return view('connectwithstrip',compact('user','message','noUser'));//->with('message', $message);

    }else{

    return view('connectwithstrip',compact('user', 'error'));//->with('error', 'Something went wrong!');
    }
  }


	public function disConnectTherapistAccount($stripeConnectId){
        
        //\Stripe\Stripe::setApiKey(\Config::get('services.stripe.secret'));
		$userObj = User::where('stripe_connect_id',$stripeConnectId)->first();
		$noUser = 1;
		if(!$userObj){
            $error = 'No user found with this account!';
            return view('connectwithstrip',compact('error','noUser'));			
		}
        $response = \Stripe\OAuth::deauthorize([
            //'client_id' => env('STRIPE_CLIENT_ID', 'ca_HTYl92c5kNYXttiGaIADjZkcBDJTruvF'),
            'client_id' => \Config::get('services.stripe.client_id'),
            'stripe_user_id' => $stripeConnectId,
        ]);

        if($response->error){
            $error = $response->error_description;
            return view('connectwithstrip',compact('error','noUser'));
            
        } else if($response->stripe_user_id){

            $userObj->stripe_connect_id = null;
            $hasSaved = $userObj->save();
            $message = 'Payment method removed successfully!';
            return view('connectwithstrip',compact('message'));            
        } else { 

            $error = 'Something  went wrong!';
            return view('connectwithstrip',compact('error','noUser'));            
        }
        //return redirect('/connectwithstrip');    
    }

}
