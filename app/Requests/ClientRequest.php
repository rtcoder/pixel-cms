<?php

namespace App\Http\Requests;


use App\Client;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'slug' => $this->method() !== 'PUT' ? 'required|unique:clients,slug' :
                [
                    'nullable',
                    'min:3',
                    function ($attribute, $value, $fail) {
                        if (Client::where([
                            ['id', '!=', $this->route('client')->id],
                            ['slug', $value]
                        ])->first())
                            $fail("Client with this slug already exists.");
                    }
                ],
            'locale' => $this->method() !== 'PUT' ? [
                'required',
                'string',
            ] : 'nullable',
            'available_locales' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (!in_array($this->method() !== 'PUT' ? $this->get('locale') : $this->route('client')->locale, $value)) {
                        $fail('Default locale must be included in available values array.');
                    }
                },
            ],
            'name' => 'required|string',
            'email' => $this->method() !== 'PUT' ? [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (Client::where('email', $value)->first() || User::where('email', $value)->first())
                        $fail("Email already in use by other client or user.");
                }
            ] : [
                'email',
                'nullable',
                function ($attribute, $value, $fail) {
                    $client = Client::where([
                        ['id', '!=', $this->route('client')->id],
                        ['email', $value]
                    ])->first();
                    $user = User::where([
                        ['client_id', '!=', $this->route('client')->id],
                        ['email', $value]
                    ])->first();
                    if ($client || $user)
                        $fail("Email already in use by other client or user.");
                }
            ],
            'phone_number' => 'nullable|string',
            'modules' => [
                'required',
                'array',
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response(['errors' => $validator->errors()], 400));
    }
}
