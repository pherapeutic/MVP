<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateClientRequest extends FormRequest
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
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|email|unique:users,email,'.$request->user_id.',id,deleted_at,NULL',
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
            'first_name.required' => 'This field is required',
            'first_name.max'  => 'Please enter maximum 255 characters',

            'last_name.required' => 'This field is required',
            'last_name.max'  => 'Please enter maximum 255 characters',

            'email.unique'  => 'This email already exist',
            'email.email'  => 'Please enter a valid email',
            'email.max'  => 'Please enter maximum 255 characters',
        ];
    }
}
