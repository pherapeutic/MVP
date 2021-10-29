<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TherapistProfile;
use App\Models\Languages;
use App\Models\UserLanguages;
use App\Models\CallLogs;
use Validator;
use Hash;
use Auth;
use DB;

class HomeController extends Controller
{
    
    protected $request;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function profile(Request $request, CallLogs $callLogs)
    {
        try {
            $userObj = $this->request->user();
            if ( ! $userObj) {
                throw new \Exception('Unable to get profile');
            }

            $returnArr = $userObj->getResponseArr();
            //calculate therapist rating
            $callLogs = $callLogs->getAllTherapistCallLog($userObj->id);
           
            $addRating = 0;
            $totalRating = 0;
            foreach ($callLogs as $callLog) {
                if($callLog->ratings){
                    $addRating += $callLog->ratings->rating;
                    $totalRating++;
                }
            }

            $ratingAvg = '';

            if(!empty($addRating))
                $ratingAvg = $addRating/$totalRating;

            $ratingAvg = round($ratingAvg,2);
            

            $returnArr['rating'] = $ratingAvg;

            return returnSuccessResponse('Profile detail', $returnArr);
        } catch (Exception $error) {
            return returnErrorResponse('Unable to get profile');
        }
    }

    public function changePassword(Request $request){
        $rules = [
            'password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->all());
        }
        $user = $this->request->user();
        if(!Hash::check($input['password'], $user->password)){
            return $this->notFoundResponse('Enter Correct Current Password');
        }
        $password = Hash::make($input['new_password']);
        User::where('id', $user->id)->update(['password' => $password]);

        return $this->successResponse([], 'Password change successfully');

    }

    public function changePhone(Request $request){
        $rules = [
            'phone' => 'required|numeric|unique:users,phone,NULL,id,deleted_at,NULL',
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->all());
        }
        $user = $this->request->user();
        $verificationOtp = $user->generateOtp();

        $user->verification_otp = $verificationOtp;
        $user->temp_phone = $input['phone'];
        $user->save();

        if($user){
            $message = "Your verification code is: ".$verificationOtp;
            // $this->sendTextMessage('+'.$input['phone_code'].$input['phone'], $message);
            return $this->successResponse(['user_id' => $user->id], 'Otp send successfully');
        }
        return $this->serverErrorResponse();
    }

    public function verifyPrimaryPhone(Request $request){

        $rules = [
            'user_id' => 'required',
            'otp' => 'required'
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->all());
        }

        $userObj = User::where('id', $input['user_id'])->where('verification_otp', $input['otp'])->first();
        if (!$userObj) {
            return $this->notFoundResponse('Invalid OTP');
        }
        $userObj->phone = $userObj->temp_phone;
        $userObj->temp_phone = null;
        $userObj->verification_otp = null;

        $userObj->save();

        $updatedUser = User::find($input['user_id']);
        $authToken = $updatedUser->createToken('authToken')->plainTextToken;
        $returnArr = $updatedUser->getResponseArr();
        $returnArr['auth_token'] = $authToken;

        return $this->successResponse($returnArr, 'Otp verified successfully');
    }

    public function resendPrimaryPhoneOtp(Request $request)
    {
        $rules = [
            'user_id' => 'required'
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->all());
        }

        $user = User::where('id', $input['user_id'])->first();
        if (!$user) {
            return $this->notFoundResponse('User not found with this user id');
        }

        $verificationOtp = $user->verification_otp;
        $userPhoneNumber = $user->temp_phone_code.$user->temp_phone;

        if(!$verificationOtp){
            $verificationOtp = $user->generateOtp();
            $user->verification_otp = $verificationOtp;
            $user->save();
        }

        $message = "Your verification code is: ".$verificationOtp;
        // $this->sendTextMessage('+'.$userPhoneNumber, $message);
        return $this->successResponse(['user_id' => $input['user_id']], 'Otp re-send successfully');
    }

    public function logout(Request $request)
    {
        Auth::logout();       
        $result = array(
                        "statusCode" => 200,
                        "message" => "success",
                        "data" =>'Successfully logged out'
                    );
        return response()->json($result );  
    }

    public function findTherapist(Request $request){
        $rules = [
            'latitude' => 'required',
            'longitude' => 'required'
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->all());
        }else{
            $latitude = $input['latitude'];
            $longitude = $input['longitude'];

            $therapists = TherapistProfile::select(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
            ->having('distance', '<', 50)
            ->orderBy('distance')
            ->get();

            if(!empty($therapists)){
                return returnSuccessResponse('Therapists list',$therapists);
            }else{
                return returnNotFoundResponse('Not found');   
            }
        }
    }

    public function isProBonoWork(Request $request){
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $rules = [
            'is_pro_bono_work' => 'required',
        ];
        
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            $userObj->updateUser($userObj->id, $inputArr);
            return $this->successResponse($inputArr, "Successfully update"); 
        }
    }

    public function agoraToken(Request $request){
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        $rules = [
            'channel_name' => 'required',
            'uid' => 'required',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        try{

            $appID = \Config::get('services.agora.app_id');
            $appCertificate = \Config::get('services.agora.app_certificate');

            $agoraToken = agoraCallForToken($appID, $appCertificate, $inputArr['channel_name'], $inputArr['uid']);

            if(!$agoraToken){
                $result = array(
                  "statusCode" => 404,  // $this-> successStatus
                  "message" => 'Something went wrong.'
                );
              return response()->json($result);                  
            }

            $result =  array(
                "statusCode" => 200, 
                "message" => 'Token Generated Successfully',
                "data" => [
                  'token' => $agoraToken
                ]
            );
            return response()->json($result);            

        } catch(\Exception $ex){
            $result = array(
                "statusCode" => 401,
                "message" => $ex->getMessage()
            );
            return response()->json($result );
        }
    }

    public function agoraTokenRtm(Request $request){
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        $rules = [
            //'channel_name' => 'required',
            'uid' => 'required',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        try{

            $appID = \Config::get('services.agora.app_id');
            $appCertificate = \Config::get('services.agora.app_certificate');

            $agoraToken = agoraCallForRtmToken($appID, $appCertificate, $inputArr['uid']);

            if(!$agoraToken){
                $result = array(
                  "statusCode" => 404,  // $this-> successStatus
                  "message" => 'Something went wrong.'
                );
              return response()->json($result);                  
            }

            $result =  array(
                "statusCode" => 200, 
                "message" => 'Rtm Token Generated Successfully',
                "data" => [
                  'token' => $agoraToken
                ]
            );
            return response()->json($result);            

        } catch(\Exception $ex){
            $result = array(
                "statusCode" => 401,
                "message" => $ex->getMessage()
            );
            return response()->json($result );
        }
    }

}
