<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MBまで
        ]);

        $path = Storage::disk('s3')->putFile('images', $request->file('image'));
        $url = config('filesystems.disks.s3.url') . '/' . $path;

        return response()->json([
            'message' => 'Upload successful',
            'path' => $path,
            'url' => $url,
        ]);
    }
}
