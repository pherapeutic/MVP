<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\FeedbackNotes;
use App\Models\CallLogs;
use App\Models\PaymentDetails;
use App\Models\Settings;
use Validator;
use Carbon\Carbon;
use Auth;
use Stripe;
use App\Models\User;

class CallLogsController extends Controller
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

    public function updateCallLog(Request $request, CallLogs $callLogs){

        $rules = [
            'caller_id' => 'required',
            'duration' => 'required',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        $callLogObj = $callLogs->getCallLogByCallerId($inputArr['caller_id']);
        if(!$callLogObj){
            return returnNotFoundResponse('Call log not found');            
        }

        $clientObject = User::find($callLogObj->user_id);
        $therapistObject = User::find($callLogObj->therapist_id);
        //Calllog status update
        $callLogObj->duration = $inputArr['duration'];

        // $paymentDetailsObj = PaymentDetails::where('call_logs_id', $callLogObj->id)
                                // ->where('is_captured', '0')->first();

        if($request->input('call_status')){
           /* if($request->input('call_status') == '3'){
                //Refund all amount when call decline
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
                    //$callLogObj->ended_at = Carbon::now();
                    $callLogObj->call_status = '3';
                    $callLogObj->payment_status = '3';
                    $callLogObj->updated_at = Carbon::now();
                    $hasUpdate = $callLogObj->save();            
                  }
                } catch(\Exception $ex){
                    $result = array(
                        "statusCode" => 401,
                        "message" => $ex->getMessage()
                    );
                    return response()->json($result );
                }
            } */

            //Check the call status
            $call_status = array("1", "2", "3");

            if (!in_array($inputArr['call_status'], $call_status)){
                $result = array(
                  "statusCode" => 401,  // $this-> successStatus
                  "message" => 'User not authorized to this action.'
                );
                return response()->json($result ); 
            }
            
            $callLogObj->call_status = $inputArr['call_status'];
        }
       /* if($request->input('ended_at') == '1'){
            //make payment when call end
            $appointTherapist = $callLogObj->therapist;
            try{
              //charge done for admin
              $dynamicAmount = $inputArr['duration']*100;
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
              $amount = $inputArr['duration'];
              $amountPercentage = (($amount*$applicationCharge)/100);
              $therapistAmount = $amount-$amountPercentage;

			   if(empty($therapistObject->stripe_connect_id)){
					$accountID=\Config::get('services.stripe.admin_account_id');
				}else{
					$accountInfo=$this->getAccountVerifyStripeOrNot($therapistObject->stripe_connect_id);
					if(empty($accountInfo)){
						 $accountID=\Config::get('services.stripe.admin_account_id');
					}else{
						 $accountID=$therapistObject->stripe_connect_id;
					}
		       }
		
              // Create a Transfer to a connected therapist account
              $transfer = \Stripe\Transfer::create([
                'amount' => $therapistAmount*100,
                'currency' => \Config::get('services.stripe.currency'),
                'source_transaction' => $paymentDetailsObj->charge_id,
                'destination' => $accountID,
                'transfer_group' => 'Transfer done for caller id #'.$callLogObj->id.', transfer to account:'.$accountID.' and therapist id :'.$therapistObject->id,
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
              $callLogObj->ended_at = Carbon::now();           
              $callLogObj->call_status = '2';           
              $callLogObj->payment_status = '2';    

            } catch(\Exception $ex){
                $result = array(
                    "statusCode" => 401,
                    "message" => $ex->getMessage()
                );
                return response()->json($result );
            }
        }*/
		$callLogObj->ended_at = Carbon::now();           
	    $callLogObj->call_status = '2';           
	    $callLogObj->payment_status = '2';    
        $callLogObj->updated_at = Carbon::now();
        $clientData = $clientObject->getResponseCalletIdArr();
         $notificationData = [
            'fcm_token' => $therapistObject->fcm_token,
            'device_type' => $therapistObject->device_type,
            'title' => ' Your call will end shortly.',
            'message' => '',
            'data' => $clientData
        ];


       //  if(($inputArr['duration'] >= 120) && $callLogObj->notification_sent_to_therapist!=1){
       //  $callLogObj->sendNotificationToTherapist($notificationData);
       //  $callLogObj->notification_sent_to_therapist = 1;
       // }
        $hasUpdate = $callLogObj->save();

        if($hasUpdate){
            return returnSuccessResponse('Update call log');
        }else{
            return returnNotFoundResponse('Not found');
        }
    }

    public function getTherapistCallLog(Request $request, CallLogs $callLogs){

        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $callLogs = $callLogs->getAllTherapistCallLog($userObj->id);

        foreach ($callLogs as $callLog) {
            $rating = Rating::where('call_logs_id', '=', $callLog->id)->first();
            if($rating){
                $callLog['rating'] = $rating->rating;
                $callLog['comment'] = $rating->comment;
            }else{
                $callLog['rating'] ="0";
                $callLog['comment'] ="";
            }
        }

        if(!empty($callLogs)){
            return returnSuccessResponse('Call log list',$callLogs);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }

    public function getClientCallLog(Request $request, CallLogs $callLogs){

        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $callLogs = $callLogs->getAllClientCallLog($userObj->id);
        foreach ($callLogs as $callLog) {
            $rating = Rating::where('call_logs_id', $callLog->id)->first();
            if($rating){
                $callLogs['rating'] = $rating->rating;
                $callLogs['comment'] = $rating->comment;                
            }else{
                $callLogss['rating'] ="0";
                $callLogss['comment'] ="";
            }
        }

        if(!empty($callLogs)){
            return returnSuccessResponse('Call log list',$callLogs);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }
    
	/**
     * Created By Anil Dogra
     * Created At 31-03-2021
     * @var $request object of request class
     * @var $user object of user class
     * @return object with registered user id
     * This function use to get account verify stripe or not
     */
	 
	public function getAccountVerifyStripeOrNot($accountID){
		 $secretId = \Config::get('services.stripe.secret');
		 $stripe = new \Stripe\StripeClient($secretId);
		  $account=$stripe->accounts->all([]);
		  // print_r($account);die;
			foreach($account as $key=>$value){
				$arrayAcount[]=$value->id;
			}
			
			if(in_array($accountID, $arrayAcount)){
				return true;
			}else{
				return false;
			}
	}
}
