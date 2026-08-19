<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AcceptPersonInvitationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $token = (string) $this->input('token', '');

        $this->merge([
            'token' => mb_strtoupper((string) preg_replace('/[\s-]+/', '', $token)),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'token' => ['required', 'string', 'size:6', 'alpha_num'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
