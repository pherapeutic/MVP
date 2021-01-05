<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Languages;
use App\Models\UserLanguages;
use App\Models\TherapistProfile;
use Carbon\Carbon;
use Validator;

class RegisterController extends Controller
{
    /**
     * Created By Devendra Rajput
     * Created At 26-10-2020
     * @var $request object of request class
     * @var $user object of user class
     * @return object with registered user id
     * This function use to register a new user
     */
    public function register(Request $request, User $user)
    {

        
        if($request->role =="Therapist"){
            $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required',
            'language_id' => 'required',
            'address' => 'required',
            'experience' => 'required',
            'qaulification' => 'required',
            'specialism' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            ];
        }else{
            $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required',
            'language_id' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            ];

        }

        $inputArr = $request->all();
       //echo"<pre>";print_r($inputArr);die;
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }
        
        if($inputArr['role'] == "Therapist"){
            $userprofile = array();
            $userprofile['address'] = $inputArr['address'];
            $userprofile['experience'] = $inputArr['experience'];
            $userprofile['qaulification'] = $inputArr['qaulification'];
            $userprofile['specialism'] = $inputArr['specialism'];
            unset($inputArr['address']);
            unset($inputArr['experience']);
            unset($inputArr['qaulification']);
            unset($inputArr['specialism']);
        }

        $userlanguages = $inputArr['language_id'];

        unset($inputArr['confirm_password']);
        unset($inputArr['language_id']);  

        $userObj = $user->saveNewUser($inputArr);
        $userprofile['user_id'] =$userObj->id;
        if($inputArr['role'] == "Therapist"){
            $Therapistprofile = new TherapistProfile();
            $Therapistprofile->saveTherapistProfile($userprofile);
        }

        $userlag = new UserLanguages();
        $userlag->saveNewUserLanguages(['user_id'=>$userObj->id,'language_id'=>$userlanguages]);
        if ($userObj) {
            $message = "Your verification code is: ".$userObj->verification_otp;
            return $this->successResponse(['user_id' => $userObj->id], 'User registered successfully');
        }

        return $this->serverErrorResponse();
    }

    /**
     * Created By Devendra Rajput
     * Created At 26-10-2020
     * @var $request object of request class
     * @var $user object of user class
     * @return object with verified user detail and auth token
     * This function use to verify uer's phone number
     */
    public function verifyOtp(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required',
            'otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        $userObj = User::where('id', $inputArr['user_id'])->where('verification_otp', $inputArr['otp'])->first();
        if (!$userObj) {
            return $this->notFoundResponse('Invalid OTP');
        }

        $userObj->email_verified_at = Carbon::now();
        $userObj->verification_otp = null;
        $userObj->save();
        $updatedUser = User::find($inputArr['user_id']);

        $authToken = $updatedUser->createToken('authToken')->plainTextToken;
        $returnArr = $updatedUser->getResponseArr();

        $userlanguage = new UserLanguages();
        $userlang = $userlanguage->getUserLanguagesById($userObj->id);
        if($userlang){
          $returnArr['language_id'] = $userlang->language_id;  
        }
        
        if($userObj->role =="Therapist"){
            $therapist = new TherapistProfile();
            $profile = $therapist->getTherapistProfileById($userObj->id);
            if($profile){
                $returnArr['address'] = $profile->address;      
                $returnArr['latitude'] = $profile->latitude;      
                $returnArr['longitude'] = $profile->longitude;      
                $returnArr['experience'] = $profile->experience;      
                $returnArr['qaulification'] = $profile->qaulification;      
            }  
        }
        $returnArr['auth_token'] = $authToken;
        return $this->successResponse($returnArr, 'Otp verified successfully');
    }

    public function resendOtp(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        $userObj = User::where('id', $inputArr['user_id'])->first();
        if (!$userObj) {
            return $this->notFoundResponse('User not found with this user id');
        }

        $verificationOtp = $userObj->verification_otp;

        if(!$verificationOtp){
            $verificationOtp = $userObj->generateOtp();
            $userObj->verification_otp = $verificationOtp;
            $userObj->save();
        }

        $message = "Your verification code is: ".$verificationOtp;
        return $this->successResponse(['user_id' => $inputArr['user_id']], 'Otp re-send successfully');
    }
}
