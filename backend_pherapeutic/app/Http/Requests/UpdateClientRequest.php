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
        $userId = ($this->segment(4)) ? ($this->segment(4)) : ('NULL');

        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id,deleted_at,NULL',
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
            'languages.required'  => 'Please select languages',
        ];
    }
}
