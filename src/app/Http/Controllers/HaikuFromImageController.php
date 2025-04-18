<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class HaikuFromImageController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MBまで
        ]);

        // 画像をbase64に変換
        $image = $request->file('image');
        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mime = $image->getMimeType();

        // Gemini API へ POST
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [[
                'parts' => [
                    [
                        'inline_data' => [
                            'mime_type' => $mime,
                            'data' => $base64,
                        ]
                    ],
                    [
                        'text' => <<<PROMPT
You are a skilled haiku poet. Based on the contents and mood of the image provided, generate a 3-line haiku in English.

- Follow 5-7-5 syllable pattern.
- Keep it minimal, emotional, and nature-inspired if appropriate.
- Output only the haiku.
PROMPT
                    ]
                ]
            ]]
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Gemini API error', 'details' => $response->body()], 500);
        }

        // Gemini応答からSVG部分を取り出し
        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

//        $path = Storage::disk('s3')->putFile('haiku-image', $text);
//        $url = config('filesystems.disks.s3.url') . '/' . $path;

        return response()->json([
            'message' => 'Upload successful',
            'svg' => $text,
//            'path' => $path,
//            'url' => $url,
        ]);
    }
}
