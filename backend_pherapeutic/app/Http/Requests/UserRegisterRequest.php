<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rulesArr = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            //'email' => 'required|string|email|max:255|unique:users,email,deleted_at,NULL',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'role' => 'required|boolean',
            'languages' => 'required',
            'device_type' => 'required|boolean',
            'fcm_token' => 'required'            
        ];

        if($request->role == \App\Models\User::THERAPIST_ROLE){
            $rulesArr['experience'] = 'required';
            $rulesArr['qualification'] = 'required';
            $rulesArr['specialism'] = 'required';
            $rulesArr['address'] = 'required';
            $rulesArr['latitude'] = 'required';
            $rulesArr['longitude'] = 'required';
        }
        return $rulesArr;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return  [
            'first_name.required' => 'Please enter first name',
            'last_name.required'  => 'Please enter last name',

            'email.required'  => 'Please enter your email',
            'email.unique'  => 'This email already exist',
            'email.email'  => 'Please enter a valid email',

            'password.required' => 'Please enter the password',
            'password.max' => 'Please enter maximum 255 characters',

            'confirm_password.same' => "Password and confirm password doesn't match",
            'confirm_password.required' => 'Please enter confirm password',

            'role.required'  => 'Please select your role',
            'role.boolean' => 'Please select a valid role',
            'languages.required'  => 'Please select languages which you can speak',

            'device_type.required'  => 'Please select device type',
            'device_type.boolean' => 'Invalid device type',
            'fcm_token.required'  => 'Please enter fcm token',

            'experience.required' => 'Please enter your experience',
            'qualification.required' => 'Please enter your qualification',
            'specialism.required' => 'Please enter your specialism',
            'address.required' => 'Please enter your address',
            'latitude.required' => 'Please enter your latitude',
            'longitude.required' => 'Please enter your longitude'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
    }
}
