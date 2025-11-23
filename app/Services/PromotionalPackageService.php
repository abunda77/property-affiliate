<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class PromotionalPackageService
{
    /**
     * Generate promotional package for a property and affiliate
     */
    public function generatePackage(Property $property, User $affiliate): string
    {
        $tempDir = 'temp/promo-' . Str::random(16);
        Storage::disk('local')->makeDirectory($tempDir);
        
        try {
            // Collect property images
            $this->collectImages($property, $tempDir);
            
            // Generate text file with property description and affiliate link
            $this->generateTextFile($property, $affiliate, $tempDir);
            
            // Create social media optimized image
            $this->createSocialMediaImage($property, $affiliate, $tempDir);
            
            // Package files into ZIP
            $zipPath = $this->createZipArchive($property, $tempDir);
            
            return $zipPath;
        } finally {
            // Clean up temporary directory
            Storage::disk('local')->deleteDirectory($tempDir);
        }
    }
    
    /**
     * Collect property images from media library
     */
    private function collectImages(Property $property, string $tempDir): void
    {
        $media = $property->getMedia('images');
        
        foreach ($media as $index => $mediaItem) {
            $imagePath = $mediaItem->getPath();
            
            if (file_exists($imagePath)) {
                $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                $filename = 'image-' . ($index + 1) . '.' . $extension;
                
                Storage::disk('local')->put(
                    $tempDir . '/images/' . $filename,
                    file_get_contents($imagePath)
                );
            }
        }
    }
    
    /**
     * Generate text file with property description and affiliate link
     */
    private function generateTextFile(Property $property, User $affiliate, string $tempDir): void
    {
        $affiliateLink = route('property.show', ['slug' => $property->slug]) . '?ref=' . $affiliate->affiliate_code;
        
        $content = "=== INFORMASI PROPERTI ===\n\n";
        $content .= "Judul: {$property->title}\n";
        $content .= "Lokasi: {$property->location}\n";
        $content .= "Harga: {$property->formatted_price}\n\n";
        
        $content .= "=== DESKRIPSI ===\n\n";
        $content .= strip_tags($property->description) . "\n\n";
        
        if (!empty($property->features)) {
            $content .= "=== FITUR ===\n\n";
            foreach ($property->features as $feature) {
                $content .= "• {$feature}\n";
            }
            $content .= "\n";
        }
        
        if (!empty($property->specs)) {
            $content .= "=== SPESIFIKASI ===\n\n";
            foreach ($property->specs as $key => $value) {
                $content .= "{$key}: {$value}\n";
            }
            $content .= "\n";
        }
        
        $content .= "=== LINK PROMOSI ANDA ===\n\n";
        $content .= $affiliateLink . "\n\n";
        $content .= "Bagikan link ini untuk mendapatkan komisi dari setiap prospek yang dihasilkan!\n";
        
        Storage::disk('local')->put($tempDir . '/info-properti.txt', $content);
    }
    
    /**
     * Create social media optimized image with property details
     */
    private function createSocialMediaImage(Property $property, User $affiliate, string $tempDir): void
    {
        // Get the first property image
        $firstMedia = $property->getFirstMedia('images');
        
        if (!$firstMedia) {
            return;
        }
        
        $imagePath = $firstMedia->getPath('large');
        
        if (!file_exists($imagePath)) {
            $imagePath = $firstMedia->getPath();
        }
        
        if (!file_exists($imagePath)) {
            return;
        }
        
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            // If GD is not available, just copy the original image
            Storage::disk('local')->put(
                $tempDir . '/social-media-post.jpg',
                file_get_contents($imagePath)
            );
            return;
        }
        
        // Create image with overlay text
        $sourceImage = $this->loadImage($imagePath);
        
        if (!$sourceImage) {
            return;
        }
        
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        
        // Create a new image for social media (1200x630 - optimal for Facebook/Twitter)
        $socialWidth = 1200;
        $socialHeight = 630;
        $socialImage = imagecreatetruecolor($socialWidth, $socialHeight);
        
        // Resize and copy the source image
        imagecopyresampled(
            $socialImage, $sourceImage,
            0, 0, 0, 0,
            $socialWidth, $socialHeight,
            $width, $height
        );
        
        // Add overlay with property details
        $this->addTextOverlay($socialImage, $property, $socialWidth, $socialHeight);
        
        // Save the image
        $outputPath = Storage::disk('local')->path($tempDir . '/social-media-post.jpg');
        imagejpeg($socialImage, $outputPath, 90);
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($socialImage);
    }
    
    /**
     * Load image from file
     */
    private function loadImage(string $path)
    {
        $imageInfo = getimagesize($path);
        
        if (!$imageInfo) {
            return false;
        }
        
        $mimeType = $imageInfo['mime'];
        
        return match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/gif' => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
            default => false,
        };
    }
    
    /**
     * Add text overlay to image
     */
    private function addTextOverlay($image, Property $property, int $width, int $height): void
    {
        // Create semi-transparent overlay at the bottom
        $overlayHeight = 150;
        $overlayColor = imagecolorallocatealpha($image, 0, 0, 0, 50); // Black with 50% transparency
        imagefilledrectangle($image, 0, $height - $overlayHeight, $width, $height, $overlayColor);
        
        // Set text colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $yellow = imagecolorallocate($image, 255, 215, 0);
        
        // Add property title
        $title = $this->truncateText($property->title, 50);
        $this->drawText($image, 24, 20, $height - 120, $title, $white);
        
        // Add location
        $location = $this->truncateText($property->location, 60);
        $this->drawText($image, 16, 20, $height - 80, $location, $white);
        
        // Add price
        $this->drawText($image, 28, 20, $height - 40, $property->formatted_price, $yellow);
    }
    
    /**
     * Draw text on image
     */
    private function drawText($image, int $size, int $x, int $y, string $text, int $color): void
    {
        // Use built-in font if TTF fonts are not available
        imagestring($image, 5, $x, $y, $text, $color);
    }
    
    /**
     * Truncate text to specified length
     */
    private function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length - 3) . '...';
    }
    
    /**
     * Create ZIP archive from temporary directory
     */
    private function createZipArchive(Property $property, string $tempDir): string
    {
        $zipFilename = 'promo-' . Str::slug($property->title) . '-' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create ZIP archive');
        }
        
        // Add all files from temp directory to ZIP
        $files = Storage::disk('local')->allFiles($tempDir);
        
        foreach ($files as $file) {
            $filePath = Storage::disk('local')->path($file);
            $relativePath = str_replace($tempDir . '/', '', $file);
            
            $zip->addFile($filePath, $relativePath);
        }
        
        $zip->close();
        
        return $zipPath;
    }
    
    /**
     * Clean up generated ZIP file
     */
    public function cleanupZipFile(string $zipPath): void
    {
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
    }
}
