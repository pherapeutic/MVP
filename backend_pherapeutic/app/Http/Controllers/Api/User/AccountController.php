<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\TempEmailVerify;
use App\Models\User;
use App\Models\TherapistProfile;
use App\Models\Languages;
use App\Models\UserLanguage;
use App\Models\UserTherapistType;
use App\Models\Address;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\ChangePasswordRequest;
use Validator;

class AccountController extends Controller
{
    public function update(UserUpdateRequest $request, UserLanguage $userLanguage, TherapistProfile $therapistProfile, UserTherapistType $userTherapistType){
      
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $inputArr = $request->all();
        $userArr = [
            'first_name' => $inputArr['first_name'],
            'last_name' => $inputArr['last_name'],
            'image' => (isset($inputArr['image']) && $inputArr['image']) ? ($inputArr['image']) : (null)
        ];

        if($userArr['image']){
            $userArr['old_image'] = $userObj->image;
        }

        $hasUpdated = $userObj->updateUser($userObj->id, $userArr);
        if(!$hasUpdated){
            return returnErrorResponse('Unable to update profile');
        }

        $languagesArr = $request->get('languages');
        //dd($languagesArr);
        /*if(is_array($languagesArr)){
            UserLanguage::where('user_id', $userObj->id)->delete();
            foreach ($languagesArr as $key => $languageId) {
                $userLanguageArr = [
                    'user_id' => $userObj->id,
                    'language_id' => $languageId
                ];
               
                $userLanguage->saveNewUserLanguages($userLanguageArr);
            }
        }*/
        // $userLanguageArr['user_id'] = $userObj->id;
        // $userLanguageArr['language_id'] = $languagesArr;
        // $user_Language = $userLanguage->saveNewUserLanguages($userLanguageArr);

        // if($userObj->role == User::CLIENT_ROLE){
        //     $userLanguageArr1 = [
        //         'user_id' => $userObj->id,
        //         'language_id' => $request->get('language_id'),
              
        //     ];
        //     $user_Language=$userLanguage1->updateUserLanguages($userLanguageArr1);
       
        if($userObj->role == User::CLIENT_ROLE){
            $userLanguageArr= [
                'user_id' => $userObj->id,
                'language_id' => $languagesArr
               
            ];
            $userLanguage->updateUserLanguagesnew($userLanguageArr);
        }

        if($userObj->role == User::THERAPIST_ROLE){
            $therapistPofileArr = [
                'user_id' => $userObj->id,
                'experience' => $request->get('experience'),
                'qualification' => $request->get('qualification'),
                'address' => $request->get('address'),
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude')
            ];
            $therapistProfile->updateTherapistProfile($therapistPofileArr);
        

            $therapistTypesArr = $request->get('specialism');
            if(is_array($therapistTypesArr)){
                UserTherapistType::where('user_id', $userObj->id)->delete();
                foreach ($therapistTypesArr as $key => $therapistTypeId) {
                    $userTherapistTypeArr = [
                        'user_id' => $userObj->id,
                        'therapist_type_id' => $therapistTypeId
                    ];
                    $userTherapistType->saveNewUserTherapistTypes($userTherapistTypeArr);
                }
            }
        }

        $updatedUser = User::find($userObj->id);
        $returnArr = $updatedUser->getResponseArr();
        $authToken = $updatedUser->createToken('authToken')->plainTextToken;
        $returnArr['auth_token'] = $authToken;
        return returnSuccessResponse('User updated successfully', $returnArr);  
    }

    public function changeOnlineStatus(Request $request){
        
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        
        //Update latitude and longitude when change the status.
        if($userObj->therapistProfile){
            $profileObj = $userObj->therapistProfile;
            $profileObj->latitude = ($request->get('latitude') ? ($request->get('latitude')) : $profileObj->latitude);
            $profileObj->longitude = ($request->get('longitude') ? ($request->get('longitude')) : $profileObj->longitude);

            $hasUpdatedProfile = $profileObj->save();            
        }

        $userObj->online_status = ($userObj->online_status == '0') ? ('1') : ('0');
        $hasUpdated = $userObj->save();

        if($hasUpdated){
            $updatedUser = User::find($userObj->id);
            $returnArr = $updatedUser->getResponseArr();
            $returnArr['auth_token'] = $request->bearerToken();
            return returnSuccessResponse('Online status updated successfully', $returnArr);
        }
        return returnErrorResponse('Unable to update online status');
    }

    public function changeNotificationStatus(Request $request){
        
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $userObj->notification_status = ($userObj->notification_status == '0') ? ('1') : ('0');
        $hasUpdated = $userObj->save();

        if($hasUpdated){
            $updatedUser = User::find($userObj->id);
            $returnArr = $updatedUser->getResponseArr();
            $returnArr['auth_token'] = $request->bearerToken();
            return returnSuccessResponse('Notification status updated successfully', $returnArr);
        }
        return returnErrorResponse('Unable to update online status');
    }

    public function changePassword(ChangePasswordRequest $request){
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        if(!Hash::check($request->old_password, $userObj->password)){
            throw new HttpResponseException(returnValidationErrorResponse('Invalid old password'));
        }

        $inputArr['password'] = Hash::make($request->get('password'));
        $hasUpdated = $userObj->updateUser($userObj->id, $inputArr);
        if(!$hasUpdated){
            return returnErrorResponse('Unable to update profile');
        }

        $updatedUser = User::find($userObj->id);
        $returnArr = $updatedUser->getResponseArr();
        $authToken = $updatedUser->createToken('authToken')->plainTextToken;
        $returnArr['auth_token'] = $authToken;
        return returnSuccessResponse('Password updated successfully', $returnArr);
    }

    // public function changeEmailVerify(Request $request){
    //     $userObj = $this->request->user();
    //     if (!$userObj) {
    //         return $this->notAuthorizedResponse('User is not authorized');
    //     }

    //     $rules = [
    //         'user_id' => 'required',
    //         'otp' => 'required'
    //     ];

    //     $input = $request->all();
    //     $validator = Validator::make($input, $rules);
    //     if ($validator->fails()) {
    //         return $this->validationErrorResponse($validator->errors()->all());
    //     }

    //     $userObj = User::where('id', $input['user_id'])->where('verification_otp', $input['otp'])->first();
    //     if (!$userObj) {
    //         return $this->notFoundResponse('Invalid OTP');
    //     }
    //     $userObj->email = $userObj->temp_email;
    //     $userObj->temp_email = null;
    //     $userObj->verification_otp = null;

    //     $userObj->save();

    //     return $this->successResponse([], 'Otp verified successfully');

    // }

    // public function resendPrimaryEmailOtp(Request $request)
    // {
    //     $rules = [
    //         'user_id' => 'required'
    //     ];

    //     $input = $request->all();
    //     $validator = Validator::make($input, $rules);
    //     if ($validator->fails()) {
    //         return $this->validationErrorResponse($validator->errors()->all());
    //     }

    //     $user = User::where('id', $input['user_id'])->first();
    //     if (!$user) {
    //         return $this->notFoundResponse('User not found with this user id');
    //     }

    //     $verificationOtp = $user->verification_otp;

    //     if(!$verificationOtp){
    //         $verificationOtp = $user->generateOtp();
    //         $user->verification_otp = $verificationOtp;
    //         $user->save();
    //     }
        
    //     return $this->successResponse(['user_id' => $input['user_id']], 'Otp re-send successfully');
    // } 

    public function logout(Request $request)
    {
        $userObj = $request->user();
        if (!$userObj) {
            return notAuthorizedResponse('You are not authorized');
        }

        $userObj->tokens()->delete();
        $userObj->fcm_token = null;
        $userObj->save();
        return returnSuccessResponse('User logged out successfully');
    }
}
