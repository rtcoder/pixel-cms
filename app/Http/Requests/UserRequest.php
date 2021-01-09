<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'email',
                'nullable',
                function ($attribute, $value, $fail) {
                    if (User::where([
                        ['id', '!=', $this->route('id')],
                        ['email', $value]
                    ])->first())
                        $fail(__('messages.email_taken'));
                }
            ],
            'name' => 'required|min:3',
            'is_active' => 'boolean',
            'role_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Role::where('id', $value)
                        ->where('client_id', Auth::user()->client_id)->first())
                        $fail(__('messages.role_not_exists'));
                }
            ],
            'password' => 'string|nullable|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'password_confirm' => 'required_with:password|same:password'
        ];
    }
}
