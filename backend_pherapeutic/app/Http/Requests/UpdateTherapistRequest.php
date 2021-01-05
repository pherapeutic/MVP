<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateTherapistRequest extends FormRequest
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
        // return [
        //     'user[first_name]' => 'required',
        //     'user[last_name]' => 'required',
        //     'user[email]' => 'nullable|email|unique:users,email,'.$request->user_id.'NULL,id,deleted_at,NULL',
        //     'user[password]' => 'required',
        //     'user[confirm_password]' => 'required|same:password',
        //     'profile[address]' => 'required',
        //     'profile[experience]' => 'required',
        //     'profile[qaulification]' => 'required'

        // ];


        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email|unique:users,email,'.$request->user_id.'NULL,id,deleted_at,NULL',
            // 'password' => 'required',
            // 'confirm_password' => 'required|same:password',
            'address' => 'required',
            'experience' => 'required',
            'qaulification' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        // return [
        //     'user[first_name].required' => 'This field is required',
        //     'user[first_name].max' => 'Please enter maximum 255 characters',

        //     'user[last_name].required' => 'This field is required',
        //     'user[last_name].max' => 'Please enter maximum 255 characters',

        //     'profile[address].required' => 'This field is required',
        //     'profile[experience].required' => 'This field is required',
        //     'profile[qaulification].required' => 'This field is required',

        //     'user[email].unique'  => 'This email already exist',
        //     'user[email].email'  => 'Please enter a valid email',
        //     'user[email].max'  => 'Please enter maximum 255 characters',


        //     'user[password].required' => 'This field is required',
        //     'user[password].max' => 'Please enter maximum 255 characters',

        //     'user[confirm_password].same' => "Password and confirm password doesn't match",
        //     'user[confirm_password].required' => 'Please enter confirm password',
            
        // ];

        return [
            'first_name.required' => 'This field is required',
            'first_name.max' => 'Please enter maximum 255 characters',

            'last_name.required' => 'This field is required',
            'last_name.max' => 'Please enter maximum 255 characters',

            'address.required' => 'This field is required',
            'experience.required' => 'This field is required',
            'qaulification.required' => 'This field is required',

            'email.unique'  => 'This email already exist',
            'email.email'  => 'Please enter a valid email',
            'email.max'  => 'Please enter maximum 255 characters',


            // 'password.required' => 'This field is required',
            // 'password.max' => 'Please enter maximum 255 characters',

            // 'confirm_password.same' => "Password and confirm password doesn't match",
            // 'confirm_password.required' => 'Please enter confirm password',
            
        ];
    }
}
