<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidImageContent implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        // Check if file is actually an image by reading its content
        $imageInfo = @getimagesize($value->getRealPath());

        if ($imageInfo === false) {
            $fail('The :attribute must be a valid image file.');
            return;
        }

        // Verify MIME type matches file extension
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        if (!in_array($imageInfo['mime'], $allowedMimeTypes)) {
            $fail('The :attribute must be a jpeg, jpg, png, gif, or webp image.');
            return;
        }

        // Check for suspicious content (basic check)
        $fileContent = file_get_contents($value->getRealPath(), false, null, 0, 1024);

        // Check for PHP tags in image files (basic security check)
        if (stripos($fileContent, '<?php') !== false || stripos($fileContent, '<?=') !== false) {
            $fail('The :attribute contains suspicious content.');
            return;
        }

        // Check for script tags
        if (stripos($fileContent, '<script') !== false) {
            $fail('The :attribute contains suspicious content.');
            return;
        }
    }
}
