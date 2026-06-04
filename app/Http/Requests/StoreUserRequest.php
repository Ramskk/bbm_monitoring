<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'unique:users,email,deleted_at,NULL,deleted_at',
            ],
            'password' => [
                'required_unless:is_update',
                Password::min(8)->letters()->numbers(),
            ],
            'role' => 'required|exists:roles,id',
            'departemen' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'deleted_at' => 'nullable|date',
        ];
    }

    /**
     * Custom validation with helpers.
     */
    public function withValidator($validator): void
    {
        $role = $this->input('role');
        if ($role && !User::where('roles.id', $role)->exists()) {
            $validator->errors()->add('role', 'Role tidak valid.');
        }

        // Cek apakah user dengan email sudah ada (soft deleted)
        $existing = User::where('email', $this->email)->whereNull('deleted_at')->first();
        if ($existing) {
            $validator->errors()->add('email', 'Email sudah terdaftar.');
        }
    }
}
