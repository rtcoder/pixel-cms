<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules(): array
    {
        return [
            'first_name' => 'string|nullable|min:3',
            'last_name' => 'string|required|min:3',
            'phone_numbers' => 'array|nullable|max:10',
            'company' => 'string|nullable',
        ];
    }
}
