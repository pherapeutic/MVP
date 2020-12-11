<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
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
    public function rules()
    {
        return [
            'old_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'old_password.required' => 'Please enter old password',
            'password.required' => 'Please enter the password',
            'confirm_password.same' => "Password and confirm password doesn't match",
            'confirm_password.required' => 'Please enter confirm password'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors()->all();
        throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
    }
}
