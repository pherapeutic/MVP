<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\Http\Controllers\Controller;
use App\User;
use App\Driver;
use App\OpenOrder;
use App\OrderDriver;
use App\OrderReview;
use Notification;
use DB;
use Auth;

class NotificationController extends Controller
{

    public function __construct(){
        // 
    }

    public static function sendNotificationToTherapist($notificationData = array()) {  

        if(count($notificationData) <= 0){
            return;
        }

        $curlUrl = "https://fcm.googleapis.com/fcm/send"; 
        $push_notification_key = \Config::get('services.notification.therapist_push_notification_key');

        if($notificationData['device_type'] == '1'){ // for IOS
            $postdata = [
                "to" => $notificationData['fcm_token'],
                "notification" => [
                    "title" => $notificationData['title'],
                    "text" => $notificationData['message'],
                    "sound" => "default",
                ],
                "data" => $notificationData['data']
            ];

        \Log::info('notification data: '. print_r($postdata, true));
        } else if($notificationData['device_type'] == '0'){ // for android
            $postdata = [
                "to" => $notificationData['fcm_token'],
                "sound" => "default",
                "data" => [
                    'data' => $notificationData['data'],
                    "title" => $notificationData['title'],
                    "text" => $notificationData['message']
                ]
            ];
        } else {
            return;
        }

        $header = array("authorization: key=" . $push_notification_key . "", "content-type: application/json");  
        $timeout = 120;
        $curlOutput = Curl::to($curlUrl)
                        ->withHeaders($header)
                        ->withData(json_encode($postdata))
                        ->withTimeout($timeout)
                        ->post();

        \Log::info('Curl out put: '. print_r($curlOutput, true));
        return json_decode($curlOutput, true);
    }

    // public static function sendNotificationToCustomer($notificationData = array()) {  

    //     if(count($notificationData) <= 0){
    //         return;
    //     }

    //     $curlUrl = "https://fcm.googleapis.com/fcm/send"; 
    //     $push_notification_key = \Config::get('constants.customer_push_notification_key');

    //     if($notificationData['device_type'] == '2'){ // for IOS
    //         $postdata = [
    //             "to" => $notificationData['fcm_token'],
    //             "notification" => [
    //                 "title" => $notificationData['title'],
    //                 "text" => $notificationData['message'],
    //                 "sound" => "default",
    //             ],
    //             "data" => $notificationData['data']
    //         ];

    //     } else if($notificationData['device_type'] == '1'){ // for android
    //         $postdata = [
    //             "to" => $notificationData['fcm_token'],
    //             "sound" => "default",
    //             "data" => [
    //                 'data' => $notificationData['data'],
    //                 "title" => $notificationData['title'],
    //                 "text" => $notificationData['message']
    //             ]
    //         ];
    //     } else {
    //         return;
    //     }
            

    //     $header = array("authorization: key=" . $push_notification_key . "", "content-type: application/json");  
    //     $timeout = 120;
    //     $curlOutput = Curl::to($curlUrl)
    //                     ->withHeaders($header)
    //                     ->withData(json_encode($postdata))
    //                     ->withTimeout($timeout)
    //                     ->post();
        
    //     return json_decode($curlOutput, true);
    // }

    public function sendVideoCallNotificationToTherapist($therapistId = '', Request $request){
        $error="";

        if(!$therapistId && (!$request->has('therapist_id') || $request->input('therapist_id') == '')){
            $error = "Therapist id is mandatory";
        }
        
        if($error != "") {
            $result = array(
                "statusCode" => 401,  // $this-> successStatus
                "message" => $error 
            );
            return response()->json($result ); 
        }
        
        $therapistId = ($therapistId) ? ($therapistId) : ($request->input('therapist_id'));
        $userObj = User::where('id', $therapistId)->where('role','1')->first();

        if(!$userObj){
            $result = array(
                "statusCode" => 401,
                "message" => 'Therapist does not exist'
            );
            return response()->json($result);
        }

        if($userObj->notification_status != '1'){

            $result = array(
                "statusCode" => 401,
                "message" => 'Therapist notification has off'
            );
            return response()->json($result);
        }

        $clientObj = Auth::user();        

        $fcmToken = $userObj->fcm_token;
        // $fcmToken = 'fZEO0-GlTQifO6qH4xjx__:APA91bFqQlFf01H6JHwQYMLzTUi4ItmYCshWpRcT6rrlhDKQfERBfhcEPcD1bsAmG-xNvRxPhEUxkeZc7ptrEZ10hZTOkcHAvC_P_nV_9rnyHNBmK4xPCwf6aTfw7wJFR_Cnl2TR5N_h';
        
        $notificationData = [
            'fcm_token' => $fcmToken,
            'device_type' => $userObj->device_type,
            'title' => 'Get Video Call From Pherapeutic. Client Id: '.$clientObj->id,
            'message' => 'For help',
            'data' => $clientObj->getResponseArr()
        ];

        $response = $this->sendNotificationToTherapist($notificationData);

        if($response['success'] == 0 && $response['failure'] == 1){
            \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
        }


        $result = array(
            "statusCode" => 200,
            "message" => 'Video call notification has been send to Therapist successfully'
        );
        return response()->json($result);        

    }

  //   public function sendNewOrderNotificationToDriver($orderId = '', Request $request) {
  //       $error="";

		// if(!$orderId && (!$request->has('order_id') || $request->input('order_id') == '')){
		// 	$error = "Order id is mandatory";
  //       }
        
		// if($error != "") {
		// 	$result = array(
		// 		"statusCode" => 401,  // $this-> successStatus
		// 		"message" => $error	
		// 	);
		// 	return response()->json($result ); 
		// }
        
  //       $orderId = ($orderId) ? ($orderId) : ($request->input('order_id'));
  //       $orderObj = Order::where('id', $orderId)->first();

  //       if(!$orderObj){
  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => 'Order does not exist'
  //           );
		// 	return response()->json($result);
  //       }

  //       if($orderObj->status != Order::ORDER_ACCEPTED){
  //           $orderStatus = $orderObj->status;
  //           $message = 'Order is not accepted yet';

  //           if($orderStatus == Order::ORDER_PENDING){
  //               $message = 'Order is pending';
  //           } else if($orderStatus == Order::ORDER_REJECTED){
  //               $message = 'Order rejected';
  //           } else if($orderStatus == Order::ORDER_PICKEDUP) {
  //               $message = 'Order has been picked up';
  //           } else if($orderStatus == Order::ORDER_DELIVERED){
  //               $message = 'Order has been delivered';
  //           } else if($orderStatus == Order::ORDER_CANCELED){
  //               $message = 'Order canceled';
  //           }

  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => $message
  //           );
		// 	return response()->json($result);
  //       }

  //       $shopObj = $orderObj->shop;
  //       $deliveryAddress = $orderObj->address;
  //       $deliveryTime = json_decode($orderObj->delivery_time, true);

  //       $dropOffAddress = [
  //           'name' => optional($deliveryAddress)->name,
  //           'address_1' => optional($deliveryAddress)->address_1,
  //           'address_2' => optional($deliveryAddress)->address_2,
  //           'city' => optional(optional($deliveryAddress)->city)->title,
  //           'state' => optional(optional($deliveryAddress)->state)->title,
  //           'zip_code' => optional($deliveryAddress)->zip_code,
  //           'lat' => optional($deliveryAddress)->lat,
  //           'long' => optional($deliveryAddress)->long
  //       ];

  //       $pickUpAddress = [
  //           'shop_name' => $shopObj->name,
  //           'address' => $shopObj->address,
  //           'lat' => $shopObj->lat,
  //           'long' => $shopObj->long
  //       ];

  //       $deliveryTime =  json_decode($orderObj->delivery_time, true);
  //       $estimatedTime = (isset($deliveryTime['end_time']) && $deliveryTime['end_time']) ? (\Carbon\Carbon::parse($orderObj->delivery_date.' '.$deliveryTime['end_time'].':00:00')->timestamp) : (0);
        
  //       $totalDistance = 0;

  //       if($deliveryAddress && $shopObj){
  //           if(($deliveryAddress->lat && $deliveryAddress->long) && ($shopObj->lat && $shopObj->long)){
  //               $totalDistance = $this->distanceCalculator($deliveryAddress->lat, $deliveryAddress->long, $shopObj->lat, $shopObj->long);
  //               $totalDistance = number_format($totalDistance, 2);
  //           }
  //       }	
            
  //       $orderDataArr = [
  //           'order_details' => [
  //               'order_id' => $orderObj->id,
  //               'order_price' => $orderObj->driverEarning(),
  //               'customer_name' => $orderObj->user->name,
  //               'customer_image' => $orderObj->user->image,
  //               'estimated_time' => $estimatedTime,
  //               'distance' => $totalDistance
  //           ],
  //           'address_details' => [
  //               'pick_up' => $pickUpAddress,
  //               'drop_off' => $dropOffAddress
  //           ]
  //       ];

  //       $onlineDrivers = Driver::where('online_status', '1')
  //                               ->where('notification_status', '1')
  //                               ->get();

  //       $notificationDriverCount = 0;

  //       foreach ($onlineDrivers as $key => $onlineDriver) {

  //           if(!$onlineDriver->start_time || !$onlineDriver->end_time){
  //               continue;
  //           }

  //           $isDriverHavePendingOrder = OrderDriver::where('driver_id', $onlineDriver->id)->where('is_delivered', '0')->whereNull('delivered_at')->count();
  //           if($isDriverHavePendingOrder > 0){
  //               continue;
  //           }
                
  //           if($deliveryAddress->lat && $deliveryAddress->long && $onlineDriver->lat && $onlineDriver->long){
		// 		$orderDistanceFromDriverLocation = $this->distanceCalculator($deliveryAddress->lat, $deliveryAddress->long, $onlineDriver->lat, $onlineDriver->long);
		// 		if($orderDistanceFromDriverLocation > $onlineDriver->covering_distance){
		// 			continue;
  //               }
		// 	}

  //           if(strtotime($onlineDriver->start_time) <= strtotime($deliveryTime['start_time'].":00") && strtotime($onlineDriver->end_time) >= strtotime($deliveryTime['end_time'].":00") ){

  //               $fcmToken = $onlineDriver->fcm_token;
  //               // $fcmToken = 'fZEO0-GlTQifO6qH4xjx__:APA91bFqQlFf01H6JHwQYMLzTUi4ItmYCshWpRcT6rrlhDKQfERBfhcEPcD1bsAmG-xNvRxPhEUxkeZc7ptrEZ10hZTOkcHAvC_P_nV_9rnyHNBmK4xPCwf6aTfw7wJFR_Cnl2TR5N_h';
                
  //               $notificationData = [
  //                   'fcm_token' => $fcmToken,
  //                   'device_type' => $onlineDriver->device_type,
  //                   'title' => 'New Order From MrNiceguy. Order Id: '.$orderId,
  //                   'message' => 'For $ '. $orderObj->driverEarning(),
  //                   'data' => $orderDataArr
  //               ];

  //               $response = $this->sendNotificationToDriver($notificationData);

  //               if($response['success'] == 0 && $response['failure'] == 1){
  //                   \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
  //               }

  //               $notificationDriverCount = $notificationDriverCount + 1;
  //           }
  //       }
        
  //       // $job = (new CreateOpenOrder($orderObj->id))->delay(60);
  //       // $this->dispatch($job);
  //       $isOpenOrder = OpenOrder::where('order_id', $orderObj->id)->count();
  //       if($isOpenOrder <= 0){
  //           OpenOrder::create(['order_id' => $orderObj->id]);
  //       }
        
  //       $result = array(
  //           "statusCode" => 200,
  //           "message" => 'Order notification has been send to ' . $notificationDriverCount . ' Drivers successfully',
  //           'order_status' => $orderObj->status
  //       );
  //       return response()->json($result);
  //   }

  //   public function sendFeedbackNotificationToDriver($orderId = '') {
  //       $error="";

		// if(!$orderId){
		// 	$error = "Order id is mandatory";
  //       }
        
		// if($error != "") {
		// 	$result = array(
		// 		"statusCode" => 401,  // $this-> successStatus
		// 		"message" => $error	
		// 	);
		// 	return response()->json($result ); 
		// }

  //       $orderObj = Order::where('id', $orderId)->first();

  //       if(!$orderObj){
  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => 'Order does not exist'
  //           );
		// 	return response()->json($result);
  //       }

  //       $orderReview = OrderReview::where('order_id', $orderId)->whereNotNull('driver_rating')->first();

  //       if(!$orderReview){
  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => 'No review posted by user yet'
  //           );
		// 	return response()->json($result);
  //       }

        
  //       $orderDriverObj = $orderObj->order_driver;
  //       if(!$orderDriverObj){
  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => 'This order is not accepted by any driver yet.'
  //           );
		// 	return response()->json($result);
  //       }

  //       $orderDataArr = [
  //           'order_details' => [
  //               'order_id' => $orderObj->id,
  //               'order_price' => $orderObj->driverEarning(),
  //               'rating' => $orderReview->driver_rating,
  //               'review' => $orderReview->driver_review
  //           ]
  //       ];
        
  //       $driverObj = $orderDriverObj->driver;
  //       $notificationData = [
  //           'fcm_token' => $driverObj->fcm_token,
  //           'device_type' => $driverObj->device_type,
  //           'title' => 'Rating Update!',
  //           'message' => 'Feedback given by customer.',
  //           'data' => $orderDataArr
  //       ];

  //       if($driverObj->notification_status == '1'){
  //           $response = $this->sendNotificationToDriver($notificationData);

  //           if($response['success'] == 0 && $response['failure'] == 1){
  //               \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
  //           }
  //       }   
        
            
        
  //       $result = array(
  //           "statusCode" => 200,
  //           "message" => $orderObj->user->name.' rated you',
  //           'order_status' => $orderObj->status
  //       );
  //       return response()->json($result);
  //   }

  //   public function sendConfirmOrderNotificationToCustomer($orderId = '') {
        
  //       if(!$orderId){
		// 	$result = array(
		// 		"statusCode" => 401,  // $this-> successStatus
		// 		"message" => 'Order Id is mendatory.'	
		// 	);
		// 	return response()->json($result );
  //       }
        
  //       $orderObj = Order::where('id', $orderId)->first();

  //       if(!$orderObj){
  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => 'Order does not exist'
  //           );
		// 	return response()->json($result);
  //       }

  //       // if($orderObj->status != Order::ORDER_CONFIRMED){
  //       //     $result = array(
  //       //         "statusCode" => 401,
  //       //         "message" => 'Order not confirmed .'
  //       //     );
		// // 	return response()->json($result);
  //       // }

  //       $user = $orderObj->user;

  //       $orderDataArr = [
  //           'order_details' => [
  //               'order_id' => $orderObj->id,
  //               'order_price' => $orderObj->total_price
  //           ]
  //       ];
        
  //       $notificationData = [
  //           'fcm_token' => $user->fcm_token,
  //           'device_type' => $user->device_type,
  //           'title' => 'Order Update!',
  //           'message' => 'Your order '.$orderObj->id.' has been confirm',
  //           'data' => $orderDataArr
  //       ];

  //       $response = $this->sendNotificationToCustomer($notificationData);

  //       if($response['success'] == 0 && $response['failure'] == 1){
  //           \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
  //       }

  //       $result = array(
  //           "statusCode" => 200,
  //           "message" => 'Notification has been send to ' . $user->name,
  //           'order_status' => $orderObj->status
  //       );
  //       return response()->json($result);
  //   }

  //   public function sendAcceptOrderNotificationToCustomer($orderId = '') {
        
  //       if(!$orderId){
		// 	$result = array(
		// 		"statusCode" => 401,  // $this-> successStatus
		// 		"message" => 'Order Id is mendatory.'	
		// 	);
		// 	return response()->json($result );
  //       }
        
  //       $orderObj = Order::where('id', $orderId)->first();

  //       if(!$orderObj){
  //           $result = array(
  //               "statusCode" => 401,
  //               "message" => 'Order does not exist'
  //           );
		// 	return response()->json($result);
  //       }

  //       // if($orderObj->status != Order::ORDER_ACCEPTED){
  //       //     $result = array(
  //       //         "statusCode" => 401,
  //       //         "message" => 'Order is not accepted yet.'
  //       //     );
		// // 	return response()->json($result);
  //       // }

  //       $user = $orderObj->user;

  //       $orderDataArr = [
  //           'order_details' => [
  //               'order_id' => $orderObj->id,
  //               'order_price' => $orderObj->total_price
  //           ]
  //       ];
        
  //       $notificationData = [
  //           'fcm_token' => $user->fcm_token,
  //           'device_type' => $user->device_type,
  //           'title' => 'Order Update!',
  //           'message' => 'Your order '.$orderObj->id.' has been accepted',
  //           'data' => $orderDataArr
  //       ];

  //       $response = $this->sendNotificationToCustomer($notificationData);

  //       if($response['success'] == 0 && $response['failure'] == 1){
  //           \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
  //       }

  //       $result = array(
  //           "statusCode" => 200,
  //           "message" => 'Notification has been send to ' . $user->name,
  //           'order_status' => $orderObj->status
  //       );
  //       return response()->json($result);
  //   }

    // public function sendRejectOrderNotificationToCustomer($orderId = '') {
        
    //     if(!$orderId){
	// 		$result = array(
	// 			"statusCode" => 401,  // $this-> successStatus
	// 			"message" => 'Order Id is mendatory.'	
	// 		);
	// 		return response()->json($result );
    //     }
        
    //     $orderObj = Order::where('id', $orderId)->first();

    //     if(!$orderObj){
    //         $result = array(
    //             "statusCode" => 401,
    //             "message" => 'Order does not exist'
    //         );
	// 		return response()->json($result);
    //     }

    //     // if($orderObj->status != Order::ORDER_ACCEPTED){
    //     //     $result = array(
    //     //         "statusCode" => 401,
    //     //         "message" => 'Order is not accepted yet.'
    //     //     );
	// 	// 	return response()->json($result);
    //     // }

    //     $user = $orderObj->user;

    //     $orderDataArr = [
    //         'order_details' => [
    //             'order_id' => $orderObj->id,
    //             'order_price' => $orderObj->total_price
    //         ]
    //     ];
        
    //     $notificationData = [
    //         'fcm_token' => $user->fcm_token,
    //         'device_type' => $user->device_type,
    //         'title' => 'Order Update!',
            // 'message' => 'Your order '.$orderObj->id.' has been rejected',
    //         'data' => $orderDataArr
    //     ];

    //     $response = $this->sendNotificationToCustomer($notificationData);

    //     if($response['success'] == 0 && $response['failure'] == 1){
    //         \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
    //     }

    //     $result = array(
    //         "statusCode" => 200,
    //         "message" => 'Notification has been send to ' . $user->name,
    //         'order_status' => $orderObj->status
    //     );
    //     return response()->json($result);
    // }

 //    public function sendOrderAcceptByDriverNotificationToCustomer($orderId = '') {
        
 //        if(!$orderId){
	// 		$result = array(
	// 			"statusCode" => 401,  // $this-> successStatus
	// 			"message" => 'Order Id is mendatory.'	
	// 		);
	// 		return response()->json($result );
 //        }
        
 //        $orderObj = Order::where('id', $orderId)->first();

 //        if(!$orderObj){
 //            $result = array(
 //                "statusCode" => 401,
 //                "message" => 'Order does not exist'
 //            );
	// 		return response()->json($result);
 //        }

 //        // $orderDriver = OrderDriver::where('order_id', $orderId)->first();
 //        // if(!$orderDriver){
 //        //     $result = array(
 //        //         "statusCode" => 401,
 //        //         "message" => 'Order is not accepted by driver'
 //        //     );
	// 	// 	return response()->json($result);
 //        // }

 //        $user = $orderObj->user;
 //        $orderDataArr = [
 //            'order_details' => [
 //                'order_id' => $orderObj->id,
 //                'order_price' => $orderObj->total_price
 //            ],
 //            // 'driver_details' => [
 //            //     'driver_id' => $orderDriver->driver_id
 //            // ]
 //        ];
        
 //        $notificationData = [
 //            'fcm_token' => $user->fcm_token,
 //            'device_type' => $user->device_type,
 //            'title' => 'Order Update!',
 //            'message' => 'Your order '.$orderObj->id.' has been accepted by driver',
 //            'data' => $orderDataArr
 //        ];

 //        $response = $this->sendNotificationToCustomer($notificationData);

 //        if($response['success'] == 0 && $response['failure'] == 1){
 //            \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
 //        }

 //        $result = array(
 //            "statusCode" => 200,
 //            "message" => 'Notification has been send to ' . $user->name,
 //            'order_status' => $orderObj->status
 //        );
 //        return response()->json($result);
 //    }

 //    public function sendOrderPickupNotificationToCustomer($orderId = '') {
        
 //        if(!$orderId){
	// 		$result = array(
	// 			"statusCode" => 401,  // $this-> successStatus
	// 			"message" => 'Order Id is mendatory.'	
	// 		);
	// 		return response()->json($result );
 //        }
        
 //        $orderObj = Order::where('id', $orderId)->first();

 //        if(!$orderObj){
 //            $result = array(
 //                "statusCode" => 401,
 //                "message" => 'Order does not exist'
 //            );
	// 		return response()->json($result);
 //        }

 //        // if($orderObj->status != Order::ORDER_PICKEDUP){
 //        //     $result = array(
 //        //         "statusCode" => 401,
 //        //         "message" => 'Order is not pickedup yet'
 //        //     );
	// 	// 	return response()->json($result);
 //        // }

 //        $user = $orderObj->user;
 //        $orderDriver = OrderDriver::where('order_id', $orderId)->first();

 //        $orderDataArr = [
 //            'order_details' => [
 //                'order_id' => $orderObj->id,
 //                'order_price' => $orderObj->total_price
 //            ],
 //            // 'driver_details' => [
 //            //     'driver_id' => optional($orderDriver->driver)->id
 //            // ]
 //        ];
        
 //        $notificationData = [
 //            'fcm_token' => $user->fcm_token,
 //            'device_type' => $user->device_type,
 //            'title' => 'Order Update!',
 //            'message' => 'Your order '.$orderObj->id.' has been pickedup',
 //            'data' => $orderDataArr
 //        ];

 //        $response = $this->sendNotificationToCustomer($notificationData);

 //        if($response['success'] == 0 && $response['failure'] == 1){
 //            \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
 //        }

 //        $result = array(
 //            "statusCode" => 200,
 //            "message" => 'Notification has been send to ' . $user->name,
 //            'order_status' => $orderObj->status
 //        );
 //        return response()->json($result);
 //    }

 //    public function sendOrderDeliveredNotificationToCustomer($orderId = '') {
        
 //        if(!$orderId){
	// 		$result = array(
	// 			"statusCode" => 401,  // $this-> successStatus
	// 			"message" => 'Order Id is mendatory.'	
	// 		);
	// 		return response()->json($result );
 //        }
        
 //        $orderObj = Order::where('id', $orderId)->first();

 //        if(!$orderObj){
 //            $result = array(
 //                "statusCode" => 401,
 //                "message" => 'Order does not exist'
 //            );
	// 		return response()->json($result);
 //        }

 //        // if($orderObj->status != Order::ORDER_DELIVERED){
 //        //     $result = array(
 //        //         "statusCode" => 401,
 //        //         "message" => 'Order is not delivered yet'
 //        //     );
	// 	// 	return response()->json($result);
 //        // }

 //        $user = $orderObj->user;
 //        $orderDriver = OrderDriver::where('order_id', $orderId)->first();

 //        $orderDataArr = [
 //            'order_details' => [
 //                'order_id' => $orderObj->id,
 //                'order_price' => $orderObj->total_price
 //            ],
 //            // 'driver_details' => [
 //            //     'driver_id' => $orderDriver->driver->id
 //            // ]
 //        ];
        
 //        $notificationData = [
 //            'fcm_token' => $user->fcm_token,
 //            'device_type' => $user->device_type,
 //            'title' => 'Order Update!',
 //            'message' => 'Your order '.$orderObj->id.' has been delivered',
 //            'data' => $orderDataArr
 //        ];

 //        $response = $this->sendNotificationToCustomer($notificationData);

 //        if($response['success'] == 0 && $response['failure'] == 1){
 //            \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
 //        }

 //        $result = array(
 //            "statusCode" => 200,
 //            "message" => 'Notification has been send to ' . $user->name,
 //            'order_status' => $orderObj->status
 //        );
 //        return response()->json($result);
 //    }

 //    public function sendOrderCancelNotificationToCustomer($orderId = '') {
        
 //        if(!$orderId){
	// 		$result = array(
	// 			"statusCode" => 401,  // $this-> successStatus
	// 			"message" => 'Order Id is mendatory.'	
	// 		);
	// 		return response()->json($result );
 //        }
        
 //        $orderObj = Order::where('id', $orderId)->first();

 //        if(!$orderObj){
 //            $result = array(
 //                "statusCode" => 401,
 //                "message" => 'Order does not exist'
 //            );
	// 		return response()->json($result);
 //        }

 //        // if($orderObj->status != Order::ORDER_CANCELED){
 //        //     $result = array(
 //        //         "statusCode" => 401,
 //        //         "message" => 'Order is not canceled yet'
 //        //     );
	// 	// 	return response()->json($result);
 //        // }

 //        $user = $orderObj->user;

 //        $orderDataArr = [
 //            'order_details' => [
 //                'order_id' => $orderObj->id,
 //                'order_price' => $orderObj->total_price
 //            ]
 //        ];
        
 //        $notificationData = [
 //            'fcm_token' => $user->fcm_token,
 //            'device_type' => $user->device_type,
 //            'title' => 'Order Update!',
 //            'message' => 'Your order '.$orderObj->id.' has been canceled by shop keeper',
 //            'data' => $orderDataArr
 //        ];

 //        $response = $this->sendNotificationToCustomer($notificationData);

 //        if($response['success'] == 0 && $response['failure'] == 1){
 //            \Log::info('Unable to send notification. Error: '. print_r($response['results'], true));
 //        }

 //        $result = array(
 //            "statusCode" => 200,
 //            "message" => 'Notification has been send to ' . $user->name,
 //            'order_status' => $orderObj->status
 //        );
 //        return response()->json($result);
 //    }

 //    public static function distanceCalculator($lat1, $lon1, $lat2, $lon2) { 
	// 	$lat1 = (float) $lat1;
	// 	$lon1 = (float) $lon1;
	// 	$lat2 = (float) $lat2;
	// 	$lon2 = (float) $lon2;

	// 	$pi80 = M_PI / 180; 
	// 	$lat1 *= $pi80; 
	// 	$lon1 *= $pi80; 
	// 	$lat2 *= $pi80; 
	// 	$lon2 *= $pi80; 
	// 	$r = 6372.797; // mean radius of Earth in km 
	// 	$dlat = $lat2 - $lat1; 
	// 	$dlon = $lon2 - $lon1; 
	// 	$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
	// 	$c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
	// 	$km = $r * $c; 
	// 	//echo ' '.$km; 
	// 	return $km / 1.609; // To convert in mile 
	// }
}
