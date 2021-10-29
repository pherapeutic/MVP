<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UserUpdateRequest extends FormRequest
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
        $userObj = $request->user();
        $rulesArr = [
            'first_name' => 'required',
            'last_name' => 'required',
            'languages' => 'required',          
        ];

        if($userObj->role == \App\Models\User::THERAPIST_ROLE){
            $rulesArr['experience'] = 'required';
            $rulesArr['qualification'] = 'required';
            $rulesArr['specialism'] = 'required';
            //$rulesArr['address'] = 'required';
            //$rulesArr['latitude'] = 'required';
            //$rulesArr['longitude'] = 'required';
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
        return [
            'first_name.required' => 'Please enter first name',
            'last_name.required'  => 'Please enter last name',
            'languages.required'  => 'Please select languages which you can speak',

            'experience.required' => 'Please enter your experience',
            'qualification.required' => 'Please enter your qualification',
            'specialism.required' => 'Please enter your specialism'
            //'address.required' => 'Please enter your address',
            //'latitude.required' => 'Please enter your latitude',
            //'longitude.required' => 'Please enter your longitude'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
    }
}
