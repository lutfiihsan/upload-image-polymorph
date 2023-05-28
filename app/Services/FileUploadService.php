<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function upload($file, $prefix, $attribute)
    {
        $path = $file->store($prefix, 'public');
        $media = new Media([
            'filename' => $path,
            'attribute' => $attribute,
        ]);

        return $media;
    }

    public function delete($filename)
    {
        if (Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }
    }
}
