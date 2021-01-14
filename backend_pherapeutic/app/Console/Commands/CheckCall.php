<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CallLogs;
use App\Models\PaymentDetails;
use App\Models\Settings;
use App\Models\User;
use Stripe;
use Carbon\Carbon;

class CheckCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End call and proceed payment, if call is not update in 5min';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

      $callLogObjs = CallLogs::where('ended_at',null)
                    ->where('updated_at', '<', Carbon::now()->subMinutes(5)->toDateTimeString())
                    ->where('payment_status','1')
                    ->get()->all();
      foreach ($callLogObjs as $key => $callLogObj) {
        //make payment
        $paymentDetailsObj = PaymentDetails::where('call_logs_id', $callLogObj->id)
                              ->where('is_captured', '0')->first();
        $appointTherapist = $callLogObj->therapist;

        if($callLogObj->duration > 10){

          try{
            //charge done for admin
            $dynamicAmount = $callLogObj->duration*100;
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
            $amount = $callLogObj->duration;
            $amountPercentage = (($amount*$applicationCharge)/100);
            $therapistAmount = $amount-$amountPercentage;

            // Create a Transfer to a connected therapist account
            $transfer = \Stripe\Transfer::create([
              'amount' => $therapistAmount*100,
              'currency' => \Config::get('services.stripe.currency'),
              'source_transaction' => $paymentDetailsObj->charge_id,
              'destination' => $appointTherapist->stripe_connect_id,
              'transfer_group' => 'Transfer done for caller id #'.$callLogObj->id.', transfer to account:'.$appointTherapist->stripe_connect_id.'',
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
            $hasUpdate = $callLogObj->save();      

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
              $callLogObj->ended_at = Carbon::now();
              $callLogObj->call_status = '2';
              $callLogObj->payment_status = '3';
              $hasUpdate = $callLogObj->save();            
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
        \Log::info("Check call cron is working fine!");

        $this->info('check:call Cummand Run successfully!');
    }
}
