<?php

namespace App\Http\Requests\Auth;

use App\Enums\Role;
use App\Models\ClassRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['sometimes', new Enum(Role::class)],
            'class_code' => [
                Rule::requiredIf(fn () => ($this->input('role') ?? Role::STUDENT->value) === Role::STUDENT->value),
                'nullable',
                'string',
                Rule::in(ClassRoom::ALLOWED_CODES),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'class_code.required' => 'Veuillez sélectionner votre classe (DD101, DD201, DD102 ou DD202).',
            'class_code.in' => 'Classe invalide. Choisissez DD101, DD201, DD102 ou DD202.',
        ];
    }
}
