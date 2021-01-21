<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

        protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validationErrorResponse($errors)
    {
        $response = [
            'statusCode' => 422,
            'message' => $errors,
        ];
        return $this->returnResponse($response);
    }

    protected function  serverErrorResponse()
    {
        $response = [
            'statusCode' => 500,
            'message' => 'Unable to process your request. Please try again later or contact to the administrator.'
        ];
        return $this->returnResponse($response);
    }

    protected function successResponse($data, $message = '')
    {
        $response = [
            'statusCode' => 200,
            'data' => $data,
            'message' => $message
        ];
        return $this->returnResponse($response);
    }

    protected function notFoundResponse($message)
    {
        $response = [
            'statusCode' => 422,
            'message' => $message,
        ];
        return $this->returnResponse($response);
    }

    protected function notAuthorizedResponse($message)
    {
        $response = [
            'statusCode' => 401,
            'message' => $message
        ];
        return $this->returnResponse($response);
    }
    
    public function returnResponse($response){
        return response()->json($response, $response['statusCode']);
    }

    public function sendTextMessage($recipientPhoneNumber, $textMessage){
        try {
            $twilioDetailArr = \Config::get('services.twilio');
            
            $client = new Client($twilioDetailArr['sid'], $twilioDetailArr['auth_token']);
            $client->messages->create($recipientPhoneNumber, ['from' => $twilioDetailArr['number'], 'body' => $textMessage]);
        } catch (Exception $ex) {
            \Log::info($ex->getMessage());
        }
            
    }
}
