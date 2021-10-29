<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\User;
use App\Models\Rating;
use App\Models\PaymentDetails;
use Carbon\Carbon;
use App\Models\Settings;
use Stripe;
use App\Models\CallLogs;

class PaymentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, PaymentDetails $paymentDetails, User $user)
    {
        
        if ($request->ajax()) {
            $appointmentsColl = $paymentDetails->getAllPayments();
            return datatables()->of($appointmentsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($paymentDetails) {
                    return ($paymentDetails->id) ? ($paymentDetails->id) : 'N/A';
                })
                ->addColumn('user_name', function ($paymentDetails) use ($user) {

                    $name = $user->getUserNameId($paymentDetails->callLogs->user_id);
                    
                    return ($name) ? ($name) : 'N/A';
                })
                ->addColumn('therapist_name', function ($paymentDetails) use ($user) {
                    $name = $user->getUserNameId($paymentDetails->callLogs->therapist_id);
                    return ($name) ? ($name) : 'N/A';
                })
                ->addColumn('created_at', function ($paymentDetails) {
                    return ($paymentDetails->created_at) ? (\Carbon\Carbon::parse($paymentDetails->created_at)->format('d M Y H:m A')) : 'N/A';
                })
                ->addColumn('amount', function ($paymentDetails) {
                    return ($paymentDetails->amount) ? ('£ '.$paymentDetails->amount) : 'N/A';
                })                
                ->addColumn('status', function ($paymentDetails) {
                    $status = 'N/A';
                    if($paymentDetails->is_captured == '0'){
                        $status = '<span class="badge badge-warning">Hold Payment</span>';
                    }else if($paymentDetails->is_captured == '1'){
                        $status = '<span class="badge badge-success">Payment Done</span>';
                    }else if($paymentDetails->is_captured == '2'){
                        $status = '<span class="badge badge-info">Refund</span>';
                    }else if($paymentDetails->is_captured == '3'){
                        $status = '<span class="badge badge-success">Payment And Refund</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($paymentDetails) {
                    $btn = '';
                    $btn = '<a href="payments/'.$paymentDetails->id.'" title="View"><i class="fas fa-eye mr-1"></i></a>';
					if($paymentDetails->is_captured !='3' && $paymentDetails->is_captured !='1' && $paymentDetails->is_captured !='2'){
							$btn .='<a  href="javascript:void(0);" class="refundAmount" data-id="'.$paymentDetails->id.'" data-transfer="'.$paymentDetails->charge_id.'" title="Refund" style="margin: 0px 5px; font-size: 13px;border: none;background: transparent;"><i class="fa fa-undo" aria-hidden="true" ></i></a>';
							$btn .='<a  href="javascript:void(0);" class="paidAmount" data-id="'.$paymentDetails->id.'" data-transfer="'.$paymentDetails->charge_id.'"  title="Paid" style="border: none;background: transparent;"><i class="far fa-play-circle"></i></a>';
					}
                   // $btn = '<a href="payments/'.$paymentDetails->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                   //  $btn .='<a href="javascript:void(0);" data-id="'.$paymentDetails->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('admin.payments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Appointments $appointments)
    {
        //echo"<pre>";print_r($request->all());die;
        $inputArr = $request->except(['_token']);
        $appointmentsObj = $appointments->saveNewAppointment($inputArr);
        if(!$appointmentsObj){
            return redirect()->back()->with('error_message', 'Unable to create Question. Please try again later.');
        }

        return redirect()->route('payments.index')->with('success_message', 'Appointments account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response/
     */
    public function show($id)
    {
        $paymentObj = PaymentDetails::find($id);

        if(!$paymentObj){
            return redirect()->route('admin.payments.index')->with('error_message', 'Appointment not found.');            
        }
        $callLogObj = $paymentObj->callLogs;

        return view('admin.payments.show',compact('callLogObj', 'paymentObj'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,  Appointments $appointments)
    {

        $appointments = $appointments->getQuestionById($id);
        if(!$appointments){
            return redirect()->back()->with('error_message', 'Appointments does not exist');
        }

        return view('admin.payments.edit', compact('appointments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //echo"<pre>";print_r($request->all());die;
        $appointments = new Appointments();
        $appointments = $appointments->getQuestionById($id);
        if(!$appointments){
            return redirect()->back()->with('error_message', 'This appointment does not exist');
        }

        $inputArr = $request->except(['_token', 'appointment_id', '_method']);
        $hasUpdated = $appointments->updateQuestion($id, $inputArr);

        if($hasUpdated){
            return redirect()->route('payments.index')->with('success_message', 'appointments updated successfully.');
        }
        return redirect()->back()->with('error_message', 'Unable to update payments. Please try again later.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Appointments $appointments)
    {
        $appointmentsObj = $appointments->getAppointmentsById($id);

        if(!$appointmentsObj){
            return returnNotFoundResponse('This question does not exist');
        }

        $hasDeleted = $qappointmentsObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Question deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
	
	 /**
     * refund payment the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function paidAmount($payment_id,$charge_id){
		            //make payment when call end
            // $appointTherapist = $callLogObj->therapist;
			
			 $paymentDetailsObj = PaymentDetails::where('id', $payment_id)->first();
			 $callLogObj=CallLogs::where('id',$paymentDetailsObj->call_logs_id)->first();
			 $callDuration = ($callLogObj->duration)?$callLogObj->duration:0;
			
			 
			 $therapistObject = User::find($callLogObj->therapist_id);
			  
			
            try{
              //charge done for admin
              $dynamicAmount = $callDuration*100;
              $charge = \Stripe\Charge::retrieve($charge_id);
              $charge->capture([
                'amount' => $dynamicAmount,
              ]);

              if($charge['paid']!=true){
                  $result = [
                      "statusCode" => 409, 
                      "message" => 'Transaction failed, '.$charge['failure_message'].', failed code'.$charge['failure_code'].'',

                  ];
              return returnErrorResponse($result);
              }

              $settingObj = Settings::first();
              $applicationCharge = $settingObj->app_charge;
              $actualAmount = \Config::get('services.stripe.amount');
              $amount = $callDuration;
              $amountPercentage = (($amount*$applicationCharge)/100);
              $adminAmount = $amount-$amountPercentage;
			  
              $therapistAmount = $actualAmount-$adminAmount;

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
                'source_transaction' => $charge_id,
                'destination' => $accountID,
                'transfer_group' => 'Transfer done for caller id #'.$callLogObj->id.', transfer to account:'.$accountID.' and therapist id :'.$therapistObject->id,
              ]);
			  // Create a Transfer to a connected therapist account
              \Stripe\Transfer::create([
                'amount' => $adminAmount*100,
                'currency' => \Config::get('services.stripe.currency'),
                'source_transaction' => $charge_id,
                'destination' => \Config::get('services.stripe.admin_account_id'),
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

             
                $paymentDetailsObj->is_captured = '1';            
              
              $hasUpdate=$paymentDetailsObj->save();

              if($hasUpdate){
					return returnSuccessResponse('Transfer the money into therapist bank account ');
				}else{
					return returnNotFoundResponse('Not found');
				}
            } catch(\Exception $ex){
                $result = array(
                    "statusCode" => 401,
                    "message" => $ex->getMessage()
                );
                return returnSuccessResponse($result );
            }
        }

       
	 

	 /**
     * refund payment the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function refundPayment($payment_id,$charge_id){
		  $paymentDetailsObj = PaymentDetails::where('id', $payment_id)->first();
		  $callLogObj=CallLogs::where('id',$paymentDetailsObj->call_logs_id)->get();
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
                    return returnSuccessResponse($result );
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
