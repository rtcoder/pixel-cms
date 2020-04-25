<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'first_name' => 'string|required|min:3',
            'last_name' => 'string|nullable|min:3',
            'phone_numbers' => 'array|nullable|max:10',
            'email_addresses' => 'array|nullable|max:10',
            'email_addresses.*' => 'email',
            'company' => 'string|nullable',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response(['errors' => $validator->errors()], 400));
    }
}
