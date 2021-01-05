<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\TherapistProfile;
use App\Models\Language;
use App\Models\UserLanguage;
use App\Models\UserTherapistType;
use App\Http\Requests\UserRegisterRequest;
use App\Notifications\SendEmailVerificationOTP;
use Carbon\Carbon;
use Validator;

class RegisterController extends Controller
{
    /**
     * Created By Devendra Rajput
     * Created At 06-11-2020
     * @var $request object of request class
     * @var $user object of user class
     * @return object with registered user id
     * This function use to register a new user
     */
    public function register(UserRegisterRequest $request, User $user, UserLanguage $userlanguage, TherapistProfile $therapistProfile, UserTherapistType $userTherapistType)
    {
        $languagesArr = $request->get('languages');
        $userArr = [
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'role' => $request->get('role')
        ];
        $userObj = $user->saveNewUser($userArr);

        if(!$userObj){
            return returnErrorResponse('Unable to register user. Please try again later');
        }

        if(is_array($languagesArr)){
            foreach ($languagesArr as $key => $languageId) {
                $userLanguageArr = [
                    'user_id' => $userObj->id,
                    'language_id' => $languageId
                ];
                $userlanguage->saveNewUserLanguages($userLanguageArr);
            }
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
            $therapistProfile->saveTherapistProfile($therapistPofileArr);

            $therapistTypesArr = $request->get('specialism');
            if(is_array($therapistTypesArr)){
                foreach ($therapistTypesArr as $key => $therapistTypeId) {
                    $userTherapistTypeArr = [
                        'user_id' => $userObj->id,
                        'therapist_type_id' => $therapistTypeId
                    ];
                    $userTherapistType->saveNewUserTherapistTypes($userTherapistTypeArr);
                }
            }
        }
        
        try{
            $userObj->notify(new SendEmailVerificationOTP($user));
        } catch(\Exception $ex){
            \Log::error($ex);
        }
        return returnSuccessResponse('Your account created successfully.', ['user_id' => $userObj->id]);
    }

    /**
     * Created By Devendra Rajput
     * Created At 06-11-2020
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
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = User::where('id', $inputArr['user_id'])
                        ->where('verification_otp', $inputArr['otp'])
                        ->first();
        if (!$userObj) {
            return returnNotFoundResponse('Invalid OTP');
        }

        $userObj->email_verified_at = Carbon::now();
        $userObj->verification_otp = null;
        $userObj->save();

        $updatedUser = User::find($inputArr['user_id']);
        $authToken = $updatedUser->createToken('authToken')->plainTextToken;
        $returnArr = $updatedUser->getResponseArr();
        $returnArr['auth_token'] = $authToken;
        return returnSuccessResponse('Otp verified successfully', $returnArr);
    }

    public function resendOtp(Request $request, User $user)
    {
        $userId = $request->get('user_id');
        if(!$userId){
            throw new HttpResponseException(returnValidationErrorResponse('Please send user id with this request'));
        }

        $userObj = User::where('id', $userId)->first();
        if (!$userObj) {
            return returnNotFoundResponse('User not found with this user id');
        }

        $verificationOtp = $userObj->verification_otp;
        if(!$verificationOtp){
            $verificationOtp = $userObj->generateOtp();
            $userObj->verification_otp = $verificationOtp;
            $userObj->save();
        }

        try{
            $userObj->notify(new SendEmailVerificationOTP($user));
        } catch(\Exception $ex){
            \Log::error($ex);
        }
        return returnSuccessResponse('Otp resend successfully!');
    }
}
