<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'role' => 'string|max:255',
            'parent_id' => 'nullable|string|max:255',
            'email' => 'string|max:255',
            'email_verified_at' => 'nullable|date',
            'password' => 'nullable|string|max:255|confirmed',
            'remember_token' => 'nullable|string|max:100',
        ];
    }
}
