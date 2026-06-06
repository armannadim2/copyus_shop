<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductWatermarkService
{
    private ImageManager $manager;
    private string $watermarkPath;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        $this->watermarkPath = public_path('images/icon_watermark.png');
    }

    /**
     * Store an uploaded file with a watermark applied.
     * Returns the stored path (relative to the 'public' disk).
     */
    public function storeWithWatermark(UploadedFile $file, string $directory): string
    {
        $path = $file->store($directory, 'public');
        $this->applyWatermark(Storage::disk('public')->path($path));
        return $path;
    }

    /**
     * Apply the CopyUS logo watermark to an already-stored image file.
     */
    public function applyWatermark(string $absolutePath): void
    {
        if (! file_exists($this->watermarkPath) || ! file_exists($absolutePath)) {
            return;
        }

        $image = $this->manager->read($absolutePath);
        $watermark = $this->manager->read($this->watermarkPath);

        // Scale watermark to 25% of the image width
        $targetWidth = (int) max(60, $image->width() * 0.25);
        $watermark->scale(width: $targetWidth);

        // Place at bottom-right with 10px padding, 50% opacity
        $image->place($watermark, 'bottom-right', 10, 10, 50);
        $image->save($absolutePath);
    }
}
