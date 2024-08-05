<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    protected function storeImage($imageable, $image, $directory = 'images', $disk = 'public')
    {
        if ($image) {
            $path = $image->store($directory, $disk);
            $imageable->image()->create(['path' => $path]);
        }
    }

    protected function updateImage($imageable, $image, $directory = 'images', $disk = 'public')
    {
        if ($image) {
            if ($imageable->image) {
                Storage::disk('public')->delete($imageable->image->path);
            }
            $path = $image->store($directory, $disk);
            $imageable->image()->updateOrCreate(['imageable_id' => $imageable->id] ,['path' => $path]);
        }
    }

    protected function deleteImage($imageable)
    {
        if ($imageable->image) {
            Storage::disk('public')->delete($imageable->image->path);
            $imageable->image()->delete();
        }
    }
}