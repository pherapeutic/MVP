<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'languages' => 'required'
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
            'first_name.required' => 'Please enter first name',
            'last_name.required'  => 'Please enter last name',

            'email.required'  => 'Please enter your email',
            'email.unique'  => 'This email already exist',
            'email.email'  => 'Please enter a valid email',

            'password.required' => 'Please enter the password',
            'password.max' => 'Please enter maximum 255 characters',

            'confirm_password.same' => "Password and confirm password doesn't match",
            'confirm_password.required' => 'Please enter confirm password',
            'languages.required'  => 'Please select languages',
            
        ];
    }
}
