<?php

namespace App\Http\Controllers;

use App\Repositories\MindscapeResultRepository;
use App\Services\GeminiHaikuService;
use Illuminate\Http\Request;

class HaikuFromImageController extends Controller
{
    protected $MindscapeResultRepository;
    protected $GeminiHaikuService;

    public function __construct(
        MindscapeResultRepository $MindscapeResult,
        GeminiHaikuService $GeminiHaikuService
    ) {
        $this->MindscapeResultRepository = $MindscapeResult;
        $this->GeminiHaikuService = $GeminiHaikuService;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MBまで
        ]);

        $image = $request->file('image');
        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mime = $image->getMimeType();

        try {
            $haikuText = $this->GeminiHaikuService->generateHaikuFromImage($base64, $mime);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gemini API error',
                'details' => $e->getMessage(),
            ], 500);
        }

        $MindscapeResult = $this->MindscapeResultRepository->findLatestWithoutHaiku();
        $MindscapeResult->haiku = $haikuText;
        $MindscapeResult->save();

        return response()->json([
            'message' => 'Upload successful',
            'haiku' => $haikuText,
        ]);
    }
}
