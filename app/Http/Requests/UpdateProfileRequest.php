<?php

namespace App\Http\Requests;

use App\Rules\ValidImageContent;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User can update their own profile
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-\.]+$/u', // Only letters, spaces, hyphens, and dots
            ],
            'whatsapp' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/',
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
            'whatsapp.regex' => 'WhatsApp number must be 10-15 digits.',
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

        // Sanitize whatsapp input
        if ($this->has('whatsapp')) {
            $this->merge([
                'whatsapp' => preg_replace('/[^0-9]/', '', $this->whatsapp),
            ]);
        }
    }
}
