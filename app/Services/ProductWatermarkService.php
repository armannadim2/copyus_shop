<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductWatermarkService
{
    private string $watermarkPath;

    public function __construct()
    {
        $this->watermarkPath = public_path('images/icon.png');
    }

    public function storeWithWatermark(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');
        $this->applyWatermark(Storage::disk('public')->path($path));
        return $path;
    }

    public function applyWatermark(string $absolutePath): void
    {
        if (! extension_loaded('gd') || ! file_exists($this->watermarkPath) || ! file_exists($absolutePath)) {
            return;
        }

        $info = @getimagesize($absolutePath);
        if (! $info) {
            return;
        }

        $image = $this->loadImage($absolutePath, $info['mime']);
        if (! $image) {
            return;
        }

        $watermark = @imagecreatefrompng($this->watermarkPath);
        if (! $watermark) {
            imagedestroy($image);
            return;
        }

        // Paletted (8-bit) PNGs must be truecolor for alpha operations
        imagepalettetotruecolor($watermark);

        $imgW = imagesx($image);
        $imgH = imagesy($image);
        $wmW  = imagesx($watermark);
        $wmH  = imagesy($watermark);

        // Scale watermark to 25% of image width (min 60 px)
        $targetW = (int) max(60, $imgW * 0.25);
        $targetH = (int) ($wmH * ($targetW / $wmW));

        $scaled = imagescale($watermark, $targetW, $targetH, IMG_BILINEAR_FIXED);
        imagedestroy($watermark);

        if (! $scaled) {
            imagedestroy($image);
            return;
        }

        // Reduce watermark to 50% opacity while keeping alpha shape intact
        $this->reduceOpacity($scaled, 50);

        // Composite onto the product image with proper alpha blending
        imagealphablending($image, true);
        imagecopy($image, $scaled, $imgW - $targetW - 10, $imgH - $targetH - 10, 0, 0, $targetW, $targetH);
        imagedestroy($scaled);

        $this->saveImage($image, $absolutePath, $info['mime']);
        imagedestroy($image);
    }

    private function loadImage(string $path, string $mime): GdImage|false
    {
        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default      => false,
        };
    }

    private function saveImage(GdImage $image, string $path, string $mime): void
    {
        if ($mime === 'image/png') {
            imagesavealpha($image, true);
            imagepng($image, $path);
        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($image, $path, 90);
        } else {
            imagejpeg($image, $path, 90);
        }
    }

    /**
     * Reduce every pixel's opacity by the given percentage (0 = no change, 100 = fully transparent).
     * GD alpha scale: 0 = fully opaque, 127 = fully transparent.
     */
    private function reduceOpacity(GdImage $img, int $reduceByPercent): void
    {
        imagealphablending($img, false);
        imagesavealpha($img, true);

        $w      = imagesx($img);
        $h      = imagesy($img);
        $factor = $reduceByPercent / 100.0;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $a    = ($rgba >> 24) & 0x7F;
                $newA = min(127, $a + (int) ((127 - $a) * $factor));
                imagesetpixel($img, $x, $y, imagecolorallocatealpha(
                    $img,
                    ($rgba >> 16) & 0xFF,
                    ($rgba >> 8)  & 0xFF,
                    $rgba & 0xFF,
                    $newA,
                ));
            }
        }
    }
}
