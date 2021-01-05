<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\TempEmailVerify;
use App\Models\User;
use App\Models\TherapistProfile;
use App\Models\Languages;
use App\Models\UserLanguages;
use App\Models\Address;
use Validator;
use Dirape\Token\Token;
class AccountController extends Controller
{
    public function update(Request $request){
        
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'language_id' => 'required',
             'email' => 'nullable|email|unique:users,email,'.$userObj->id.',id,deleted_at,NULL',
        ];
        if($userObj->role == "Therapist"){
            $rules['qaulification'] = 'required';
            $rules['experience'] = 'required';
            $rules['specialism'] = 'required';
        }
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }
        if($userObj->role == "Therapist"){
            $profile = new TherapistProfile();
            $Profilearr['qaulification'] = $inputArr['qaulification'];
            $Profilearr['experience'] = $inputArr['experience'];
            $Profilearr['specialism'] = $inputArr['specialism'];
            $profile->updateTherapistProfile($userObj->id,$Profilearr);
            unset($inputArr['qaulification']);
            unset($inputArr['experience']);
            unset($inputArr['specialism']);
        }
        
        $msg = 'Profile updated successfully! ';
        $sendverifyemail = false;
        if($inputArr['email'] != $userObj->email){
            $inputArr['temp_email'] = $inputArr['email'];

           // $inputArr['verify_temp_email_token'] = (new Token())->Unique('users', 'api_token', 60);
            $verificationOtp = $userObj->generateOtp();
            $inputArr['verification_otp'] = $verificationOtp;
            unset($inputArr['email']);
            $msg .= 'Please verify your email, otp 123456';
            //$sendverifyemail =true;
        }
        $userlanguage = new UserLanguages();
        $userlanguage->updateUserLanguages($userObj->id,['language_id' =>$inputArr['language_id'] ]);
        unset($inputArr['language_id']);
        $userObj->updateUser($userObj->id, $inputArr);

        if($sendverifyemail){
            $user_data = $userObj;
            $user_data->mail = $inputArr['temp_email'];
            $user_data->mail_subject = 'Please verify your email';
            $user_data->message = "Your verification code is: ".$verificationOtp;
            //$user_data->verify_url = route('emailverification');
            $user_data->notify(new TempEmailVerify($user_data));
        }
        $returnArr = $userObj->getResponseArr();

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
        
        return $this->successResponse($returnArr, $msg);  
    }

    public function changeEmailVerify(Request $request){
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

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
        $userObj->email = $userObj->temp_email;
        $userObj->temp_email = null;
        $userObj->verification_otp = null;

        $userObj->save();

        return $this->successResponse([], 'Otp verified successfully');

    }

    public function resendPrimaryEmailOtp(Request $request)
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

        if(!$verificationOtp){
            $verificationOtp = $user->generateOtp();
            $user->verification_otp = $verificationOtp;
            $user->save();
        }

            // $message = "Your verification code is: ".$verificationOtp;
            // $user_data = $userObj;
            // $user_data->mail = $inputArr['temp_email'];
            // $user_data->mail_subject = 'Please verify your email';
            // $user_data->message = $message;
            // $user_data->notify(new TempEmailVerify($user_data));
        return $this->successResponse(['user_id' => $input['user_id']], 'Otp re-send successfully');
    } 
}
