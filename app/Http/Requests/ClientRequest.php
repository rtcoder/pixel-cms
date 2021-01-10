<?php

namespace App\Http\Requests;


use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules(): array
    {
        return [
            'slug' => [
                'nullable',
                'min:3',
                function ($attribute, $value, $fail) {
                    if (Client::where([
                        ['id', '!=', $this->route('id')],
                        ['slug', $value]
                    ])->first())
                        $fail("Client with this slug already exists.");
                }
            ],
            'locale' => 'required|string',
            'available_locales' => 'required|array',
            'name' => 'required|string',
            'email' => [
                'email',
                'required',
                function ($attribute, $value, $fail) {
                    if (Client::where([
                        ['id', '!=', $this->route('id')],
                        ['email', $value]
                    ])->first())
                        $fail("Email already in use by other client.");
                }
            ],
            'phone_number' => 'nullable|string',
            'modules' => [
                'array',
            ],
        ];
    }
}
