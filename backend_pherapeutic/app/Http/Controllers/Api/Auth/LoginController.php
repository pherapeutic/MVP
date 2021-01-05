<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Languages;
use App\Models\UserLanguages;
use App\Models\TherapistProfile;
use Validator;
use Auth;

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
    {//die('hhh');
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            // return if invalid input
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        if (!Auth::attempt(['email' => $inputArr['email'], 'password' => $inputArr['password']])) {
            // return if invalid credentails
            return $this->notFoundResponse('Invalid credentials');
        }

        $userObj = User::all()->where('email', $inputArr['email'])->first();
        if ( ! Hash::check($inputArr['password'], $userObj->password, [])) {
            // return if password
            return $this->notFoundResponse('Invalid credentials');
        }
        
        $authToken = $userObj->createToken('authToken')->plainTextToken;
        $returnArr = $userObj->getResponseArr();
        $returnArr['auth_token'] = $authToken;

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
        return $this->successResponse($returnArr, 'User loggedin successfully');
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
            'email' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            // return if invalid input
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        $userObj = User::where('email', $inputArr['email'])->first();
        if (!$userObj) {
            return $this->notFoundResponse('User not found with this phone number');
        }

        $userId = $userObj->id;
        $resetPasswordOtp = $userObj->generateOtp();
        $userObj->reset_password_otp = $resetPasswordOtp;
        $userObj->save();

        $message = "Your reset password otp is: ".$resetPasswordOtp;

        return $this->successResponse(['user_id' => $userId], 'Reset password otp sent successfully');
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
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        $userObj = User::where('id', $inputArr['user_id'])->where('reset_password_otp', $inputArr['reset_password_otp'])->first();
        if (!$userObj) {
            return $this->notFoundResponse('Invalid otp');
        }

        $userObj->password = $inputArr['password'];
        $userObj->reset_password_otp = null;
        $userObj->save();

        return $this->successResponse([], 'Password reset successfully');
    }
}
