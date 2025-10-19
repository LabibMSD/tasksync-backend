<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read \App\Models\User $user
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('users', 'name')->ignore($this->user)],
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($this->user)],
            'password' => ['required', 'string'],
            'role' => ['required', 'string']
        ];
    }
}
