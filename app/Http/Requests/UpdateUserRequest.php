<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use App\Rules\ValidImageContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-\.]+$/u', // Only letters, spaces, hyphens, and dots
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
                    ->symbols(),
                'confirmed',
            ],
            'whatsapp' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/',
            ],
            'status' => [
                'required',
                Rule::enum(UserStatus::class),
            ],
            'roles' => [
                'required',
                'array',
                'min:1',
            ],
            'roles.*' => [
                'integer',
                'exists:roles,id',
            ],
            'profile_photo' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048', // 2MB
                new ValidImageContent(),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name must not exceed 255 characters.',
            'name.regex' => 'Name can only contain letters, spaces, hyphens, and dots.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'whatsapp.regex' => 'WhatsApp number must be 10-15 digits.',
            'roles.required' => 'At least one role must be assigned.',
            'roles.min' => 'At least one role must be assigned.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be jpeg, jpg, png, or gif.',
            'profile_photo.max' => 'Profile photo must not exceed 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize name input
        if ($this->has('name')) {
            $this->merge([
                'name' => strip_tags($this->name),
            ]);
        }

        // Sanitize email input
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }

        // Sanitize whatsapp input
        if ($this->has('whatsapp')) {
            $this->merge([
                'whatsapp' => preg_replace('/[^0-9]/', '', $this->whatsapp),
            ]);
        }
    }
}
