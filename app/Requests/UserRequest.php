<?php

namespace App\Http\Requests;

use App\Media;
use App\Role;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => $this->method() !== 'PUT' ? 'email|required|unique:users,email' :
                [
                    'email',
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if (User::where([
                            ['id', '!=', $this->route('user')->id],
                            ['email', $value]
                        ])->first())
                            $fail("Cannot change email, as it's already used by another user.");
                    }
                ],
            'name' => 'required|min:3',
            'is_active' => 'boolean',
            'role_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Role::where('id', $value)
                        ->where(function ($q) {
                            $q->where('client_id', Auth::user()->client_id)
                                ->orWhereNull('client_id');
                        })->first())
                        $fail("Role with id {$value} not found");
                }
            ],
            'password' => 'string|nullable|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'password_confirm' => 'required_with:password|same:password'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute field is required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response(['errors' => $validator->errors()], 400));
    }
}
