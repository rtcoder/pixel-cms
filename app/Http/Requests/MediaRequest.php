<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class MediaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'required',
        ];
    }
}
