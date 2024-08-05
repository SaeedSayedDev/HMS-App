<?php

namespace App\Http\Controllers;

use App\Models\Image;

class ImageController extends Controller
{
    public function show(Image $image)
    {
        try {
            return response()->file(storage_path('app/public/' . $image->path));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Image not found'], 404);
        }
    }
}
