<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'client_name' => 'required|regex:^[A-Za-z]+(?:\s[A-Za-z]+)+$|max:255', //makes sure the name only accepts aplabetic characters.
            'client_number' => 'required|numeric|digits:10',
            'client_email' => 'required | email |max:255|unique:clients',
            'status' => 'required | boolean',
        ];
    }
}
