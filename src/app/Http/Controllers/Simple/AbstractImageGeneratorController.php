<?php

namespace App\Http\Controllers\Simple;

use App\Http\Controllers\Controller;
use App\Repositories\MindscapeResultRepository;
use App\Services\GeminiAbstractImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbstractImageGeneratorController extends Controller
{
    protected $MindscapeResultRepository;
    protected $GeminiAbstractImageService;

    public function __construct(
        MindscapeResultRepository $MindscapeResult,
        GeminiAbstractImageService $GeminiAbstractImageService
    ) {
        $this->MindscapeResultRepository = $MindscapeResult;
        $this->GeminiAbstractImageService = $GeminiAbstractImageService;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $image = $request->file('image');
        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mime = $image->getMimeType();

        $path = Storage::disk('s3')->putFile('images', $image);
        $uploaded_image_url = config('filesystems.disks.s3.url') . '/' . $path;


        try {
            $svg = $this->GeminiAbstractImageService->generateSvgFromImage($base64, $mime);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gemini API error',
                'details' => $e->getMessage(),
            ], 500);
        }

        $filename = 'svg-' . now()->timestamp . '.svg';
        $path = 'svgs/' . $filename;
        Storage::disk('s3')->put($path, $svg);
        $mindscape_image_url = config('filesystems.disks.s3.url') . '/' . $path;

        return response()->json([
            'message' => 'Upload successful',
            'uploaded_image_url' => $uploaded_image_url,
            'mindscape_image_url' => $mindscape_image_url,
        ]);
    }
}
