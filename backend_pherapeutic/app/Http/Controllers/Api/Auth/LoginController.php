<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Language;
use App\Models\UserLanguage;
use App\Models\UserTherapistType;
use App\Models\TherapistProfile;
use App\Notifications\SendResetPasswordOTP;
use App\Notifications\SendEmailVerificationOTP;
use Validator;
use Auth;
use Carbon\Carbon;

class LoginController extends Controller
{

    /**
     * Created By Parmod kumar
     * Created At 26-10-2020
     * @var $request request object
     * @return object of authenticated user along with auth token
     * This function use to login the user
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'device_type' => 'required|boolean',
            'fcm_token' => 'required'
        ];

        $messages = [
            'email.required' => 'Please enter your email',
            'email.email' => 'Please enter a valid email',
            'password.required' => 'Please enter your password',
            'device_type.required'  => 'Please select device type',
            'device_type.boolean' => 'Invalid device type',
            'fcm_token.required'  => 'Please enter fcm token'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        if (!Auth::attempt(['email' => $inputArr['email'], 'password' => $inputArr['password']])) {
            // return if invalid credentails
            return returnNotFoundResponse('Invalid credentials');
        }

        $userObj = User::all()->where('email', $inputArr['email'])->first();
        if ( ! Hash::check($inputArr['password'], $userObj->password, [])) {
            return returnNotFoundResponse('Invalid credentials');
        }

        //Check account is otp verified
        if(!$userObj->email_verified_at){
            $returnArr['user_id'] = $userObj->id;

            $response = [
                'statusCode' => 402,
                'data' => $returnArr,
                'message' => "Email not yet verified"
            ];
            return $this->returnResponse($response);         
        }
        //end otp verified check
        $userObj->fill([
            'fcm_token' => $request->input('fcm_token')
        ]);        
        $userObj->save();

        $authToken = $userObj->createToken('authToken')->plainTextToken;
        $returnArr = $userObj->getResponseArr();
        $returnArr['auth_token'] = $authToken;

        return returnSuccessResponse('User loggedin successfully', $returnArr);
    }

    /**
     * Created By Parmod kumar
     * Created At 26-10-2020
     * @var $request object of request class
     * @var $user object of user class
     * @return object after send reset password token
     * This function use to send forgot password OTP
     */
    public function forgotPassword(Request $request, User $user)
    {
        $rules = [
            'email' => 'required|email'
        ];

        $messages = [
            'email.required' => 'Please enter your registered email',
            'email.email' => 'Please enter a valid email'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = User::where('email', $inputArr['email'])->first();
        if (!$userObj) {
            return returnNotFoundResponse('User not found with this email address');
        }

        $resetPasswordOtp = $userObj->generateOtp();
        $userObj->reset_password_token = $resetPasswordOtp;
        $userObj->save();

        try{
            $userObj->notify(new SendResetPasswordOTP());
        } catch(\Exception $ex){
            \Log::error($ex);
        }

        return returnSuccessResponse('Reset password otp sent successfully', ['user_id' => $userObj->id]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required',
            'reset_password_otp' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validationErrors = $validator->errors()->all();
            return returnValidationErrorResponse($validationErrors[0]);
        }

        $userObj = User::where('id', $inputArr['user_id'])
                        ->where('reset_password_token', $inputArr['reset_password_otp'])
                        ->first();
        if (!$userObj) {
            return returnNotFoundResponse('Invalid reset password OTP');
        }

        $userObj->password = $inputArr['password'];
        $userObj->reset_password_token = null;
        $userObj->save();

        return returnSuccessResponse('Password reset successfully');
    }

    public function socialLogin(Request $request, User $user, TherapistProfile $therapistProfile, UserTherapistType $userTherapistType, UserLanguage $userlanguage){

        //Social login when user visit second time
        $userToken = $request->get('social_token');
        $isExistUser = User::where('social_token',$userToken)->first();
        if($isExistUser){

            if($request->has('fcm_token')){
                $isExistUser->fill([
                    'fcm_token' => $request->input('fcm_token')
                ]);
                $isExistUser->save();
            }

            $authToken = $isExistUser->createToken('authToken')->plainTextToken;
            $returnArr = $isExistUser->getResponseArr();
            $returnArr['auth_token'] = $authToken;

        return returnSuccessResponse('User loggedin successfully', $returnArr);            
        }

        //Social login when user visit first time
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'login_type' => 'required',
            'social_token' => 'required|unique:users,social_token,NULL,id,deleted_at,NULL',
            'role' => 'required|boolean',
            'languages' => 'required',
            'device_type' => 'required|boolean',
            'fcm_token' => 'required',
            'image' => 'required',
        ];
        $role = $request['role'];
        //for Therapist
        if( $role =='1'){
            $rules['experience'] = 'required';
            $rules['qualification'] = 'required';
            $rules['specialism'] = 'required';
            $rules['address'] = 'required';
            $rules['latitude'] = 'required';
            $rules['longitude'] = 'required';
        }                
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        $userArr = [
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'social_token' => $request->get('social_token'),
            'login_type' => $request->get('login_type'),
            'email' => $request->get('email'),
            'role' => $request->get('role'),
            'image' => $request->get('image'),
            'email_verified_at' => Carbon::now()
        ];

        $userObj = $user->saveNewUser($userArr);

        if(!$userObj){
            return returnErrorResponse('Unable to register user. Please try again later');
        }
        $languagesArr = $request->get('languages');

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
        $authToken = $userObj->createToken('authToken')->plainTextToken;
        $returnArr = $userObj->getResponseArr();
        $returnArr['auth_token'] = $authToken;

        return returnSuccessResponse('Your account created successfully.', $returnArr);
    }
}
