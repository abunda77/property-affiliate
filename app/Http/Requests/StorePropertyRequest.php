<?php

namespace App\Http\Requests;

use App\Enums\PropertyStatus;
use App\Rules\ValidImageContent;
use App\Services\HtmlSanitizerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Property::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:properties,slug',
            ],
            'price' => [
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
            'location' => [
                'required',
                'string',
                'max:500',
            ],
            'description' => [
                'required',
                'string',
                'max:10000',
            ],
            'features' => [
                'nullable',
                'array',
                'max:50',
            ],
            'features.*' => [
                'string',
                'max:255',
            ],
            'specs' => [
                'nullable',
                'array',
                'max:50',
            ],
            'specs.*' => [
                'string',
                'max:500',
            ],
            'status' => [
                'required',
                Rule::enum(PropertyStatus::class),
            ],
            'images' => [
                'nullable',
                'array',
                'max:20',
            ],
            'images.*' => [
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120', // 5MB
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
            'title.required' => 'Title is required.',
            'title.max' => 'Title must not exceed 255 characters.',
            'slug.required' => 'Slug is required.',
            'slug.regex' => 'Slug must be lowercase alphanumeric with hyphens only.',
            'slug.unique' => 'This slug is already in use.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be at least 0.',
            'price.max' => 'Price is too large.',
            'location.required' => 'Location is required.',
            'description.required' => 'Description is required.',
            'description.max' => 'Description must not exceed 10,000 characters.',
            'features.max' => 'Maximum 50 features allowed.',
            'features.*.max' => 'Each feature must not exceed 255 characters.',
            'specs.max' => 'Maximum 50 specifications allowed.',
            'specs.*.max' => 'Each specification must not exceed 500 characters.',
            'images.max' => 'Maximum 20 images allowed.',
            'images.*.image' => 'File must be an image.',
            'images.*.mimes' => 'Image must be jpeg, jpg, png, or webp.',
            'images.*.max' => 'Each image must not exceed 5MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $sanitizer = app(HtmlSanitizerService::class);

        // Sanitize text inputs
        if ($this->has('title')) {
            $this->merge([
                'title' => $sanitizer->sanitizePlainText($this->title),
            ]);
        }

        if ($this->has('location')) {
            $this->merge([
                'location' => $sanitizer->sanitizePlainText($this->location),
            ]);
        }

        // Sanitize description (allow safe HTML)
        if ($this->has('description')) {
            $this->merge([
                'description' => $sanitizer->sanitizeRichText($this->description),
            ]);
        }

        // Sanitize features array
        if ($this->has('features') && is_array($this->features)) {
            $this->merge([
                'features' => array_map(fn($feature) => $sanitizer->sanitizePlainText($feature), $this->features),
            ]);
        }

        // Sanitize specs array
        if ($this->has('specs') && is_array($this->specs)) {
            $sanitizedSpecs = [];
            foreach ($this->specs as $key => $value) {
                $sanitizedSpecs[$sanitizer->sanitizePlainText($key)] = $sanitizer->sanitizePlainText($value);
            }
            $this->merge([
                'specs' => $sanitizedSpecs,
            ]);
        }
    }
}
