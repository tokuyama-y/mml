<?php

namespace App\Http\Controllers;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageAbstractController extends Controller
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

        $scopes = [
            'https://www.googleapis.com/auth/generative-language'
        ];

        // OAuth 2.0 トークンを取得
        $credentials = new ServiceAccountCredentials(
            $scopes,
            config('services.google.credentials_path')
        );
        $tokenData = $credentials->fetchAuthToken();

        if (!is_array($tokenData) || !isset($tokenData['access_token'])) {
            throw new \Exception("アクセストークンの取得に失敗しました: ");
        }

        $accessToken = $tokenData['access_token'];

        // Gemini API へ POST
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
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
                        'text' => <<<EOT
この画像の印象を抽象化して、心象風景のような幾何学模様に変換してください。
以下の条件でSVGコードを生成してください：

- 幾何図形（例：円、三角形、直線）のみ使用
- 背景なし、stroke-width: 1以上で描画
- 色数は1〜3色
- SVG全体のviewBoxは "0 0 500 500"
- 出力は<svg>タグから始まり</svg>タグで終わるSVGコードとしてください。
EOT
                    ]
                ]
            ]]
        ]);

        if (!$response->ok()) {
//            return response()->json(['error' => 'Gemini API error', 'details' => $response->body()], 500);
            return throw new \Exception("Gemini API error");
        }

        // Gemini応答からSVG部分を取り出し
        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $filename = 'svg-' . now()->timestamp . '.svg';
//        $path = Storage::disk('s3')->putFile('svgs/' . $filename, $text);


        $path = 'svgs/' . $filename;
        Storage::disk('s3')->put($path, $text);
        $url = config('filesystems.disks.s3.url') . '/' . $path;

        return response()->json([
            'message' => 'Upload successful',
            'svg' => $text,
            'path' => $path,
            'url' => $url,
        ]);
    }
}
