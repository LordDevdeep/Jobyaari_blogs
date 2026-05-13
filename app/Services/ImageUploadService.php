<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImageUploadService
{
    public function __construct(private ?ImageManager $manager = null)
    {
        $this->manager ??= new ImageManager(new GdDriver());
    }

    /** Returns the stored filename, relative to public/uploads/blogs/. */
    public function storeBlogImage(UploadedFile $file, ?string $slugHint = null): string
    {
        $dir = public_path('uploads/blogs');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $base = $slugHint ? Str::slug($slugHint) : Str::random(10);
        $filename = $base . '-' . Str::random(6) . '.webp';
        $absolute = $dir . DIRECTORY_SEPARATOR . $filename;

        $image = $this->manager->read($file->getRealPath());
        if ($image->width() > 1200) {
            $image->scaleDown(width: 1200);
        }
        $image->toWebp(82)->save($absolute);

        return $filename;
    }

    public function deleteBlogImage(?string $filename): void
    {
        if (! $filename) {
            return;
        }
        $path = public_path('uploads/blogs/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
