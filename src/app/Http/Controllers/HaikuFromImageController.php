<?php

namespace App\Http\Controllers;

use GeminiAPI\Enums\HarmBlockThreshold;
use GeminiAPI\Enums\HarmCategory;
use GeminiAPI\GenerationConfig;
use GeminiAPI\SafetySetting;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use GeminiAPI\Client;
use GeminiAPI\Enums\MimeType;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\ImagePart;
use GeminiAPI\Resources\Parts\TextPart;

class HaikuFromImageController extends Controller
{
    protected $client;
    protected $model = "gemini-1.5-pro-latest"; // 使用するモデル

    public function __construct()
    {
        $this->client = new Client(env('GEMINI_API_KEY'));
    }

    public function generateKaresansuiSVG($imageData)
    {
        $request = new Request([
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Generate SVG code representing karesansui sand patterns based on this image."],
                        [
                            "inlineData" => [
                                "mimeType" => "image/jpeg", // 画像タイプに応じて変更
                                "data" => $imageData // Base64エンコード済みの画像データ
                            ]
                        ]
                    ]
                ]
            ],
            "systemInstruction" => [
                "parts" => [
                    ["text" => "# ROLE\nYou are a master designer of karesansui..."]
                ]
            ],
//            "safetySettings" => [
//                ["category" => "HARM_CATEGORY_HATE_SPEECH", "threshold" => "BLOCK_ONLY_HIGH"],
//                ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => "BLOCK_ONLY_HIGH"],
//                ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_ONLY_HIGH"],
//                ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => "BLOCK_ONLY_HIGH"]
//            ]
//        ]);
        $safetySetting[] = new SafetySetting(
            HarmCategory::HARM_CATEGORY_HATE_SPEECH,
            HarmBlockThreshold::BLOCK_ONLY_HIGH,

        ),
        $safetySetting[] = new SafetySetting(
            HarmCategory::HARM_CATEGORY_SEXUALLY_EXPLICIT,
            HarmBlockThreshold::BLOCK_ONLY_HIGH,

        );
        $safetySetting[] = new SafetySetting(
            HarmCategory::HARM_CATEGORY_DANGEROUS_CONTENT,
            HarmBlockThreshold::BLOCK_ONLY_HIGH,

        );
        $safetySetting[] = new SafetySetting(
            HarmCategory::HARM_CATEGORY_HARASSMENT,
            HarmBlockThreshold::BLOCK_ONLY_HIGH,

        );

        $generationConfig = (new GenerationConfig())
            ->withTemperature(0.85)
            ->withTopP(0.9)
            ->withTopK(45)
            ->withMaxOutputTokens(2048)
            ->withStopSequences(['']);

        try {
//            $response = $this->client->generateContent($this->model, $request);
            $response = $this->client->generativeModel(ModelName::GEMINI_PRO)
                               ->withAddedSafetySetting($safetySetting)
                               ->withGenerationConfig($generationConfig)
                               ->generateContent($request);
            return $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } catch (\Exception $e) {
            \Log::error("Error requesting Gemini API: " . $e->getMessage());
            return null;
        }
    }

//
//    public function generate(Request $request)
//    {
//        $request->validate([
//            'image' => 'required|image|max:10240', // 10MBまで
//        ]);
//
//        // 画像をbase64に変換
//        $image = $request->file('image');
//        $base64 = base64_encode(file_get_contents($image->getRealPath()));
//        $mime = $image->getMimeType();
//
//        $client = new Client('GEMINI_API_KEY');
//        $response = $client->generativeModel(ModelName::GEMINI_PRO)->generateContent(
//            new TextPart('Explain what is in the image'),
//            new ImagePart(
//                $mime,
//                $base64,
//            ),
//        );
//
//        $safetySettings[] = null
//        $safetySettings[] = new SafetySetting(
//            HarmCategory::HARM_CATEGORY_HATE_SPEECH,
//            HarmBlockThreshold::BLOCK_ONLY_HIGH,
//        );
//        $safetySettings[] = new SafetySetting(
//            HarmCategory::HARM_CATEGORY_HATE_SPEECH,
//            HarmBlockThreshold::BLOCK_ONLY_HIGH,
//        );
//        $generationConfig = (new GenerationConfig())
//            ->withCandidateCount(1)
//            ->withMaxOutputTokens(40)
//            ->withTemperature(0.5)
//            ->withTopK(40)
//            ->withTopP(0.6)
//            ->withStopSequences(['STOP']);
//
//        $client = new Client('GEMINI_API_KEY');
//        $response = $client->generativeModel(ModelName::GEMINI_PRO)
//                           ->withAddedSafetySetting($safetySetting)
//                           ->withGenerationConfig($generationConfig)
//                           ->generateContent(
//                               new TextPart('PHP in less than 100 chars')
//                           );
//
//
//        $scopes = [
//            'https://www.googleapis.com/auth/generative-language.vision',
//            'https://www.googleapis.com/auth/generative-language.tuned',
//            'https://www.googleapis.com/auth/cloud-platform',
//        ];
//
//        // OAuth 2.0 トークンを取得
//        $credentials = new ServiceAccountCredentials(
//            $scopes,
//            env('GOOGLE_APPLICATION_CREDENTIALS')
//        );
//        $accessToken = $credentials->fetchAuthToken()['access_token'];
//
//        // Gemini API へ POST
//        $response = Http::withHeaders([
//            'Authorization' => 'Bearer ' . $accessToken,
//            'Content-Type' => 'application/json',
//        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
//            'contents' => [[
//                'parts' => [
//                    [
//                        'inline_data' => [
//                            'mime_type' => $mime,
//                            'data' => $base64,
//                        ]
//                    ],
//                    [
//                        'text' => 'Generate SVG code representing karesansui sand patterns based on this image.'
//                    ]
//                ]
//            ]],
//            'systemInstruction' => [[
//                'parts' => [
//                    [
//                        'text' => <<<PROMPT
//# ROLE
//You are a master designer of karesansui (枯山水) - traditional Japanese Zen rock gardens - with expertise in generating SVG code.
//
//# TASK
//Transform the provided image into SVG code that represents the sand patterns (砂紋, samon) of a karesansui garden.
//
//# TECHNICAL REQUIREMENTS
//1. **Simplification:** Extract and simplify the key features and contours from the image while maintaining its essential character.
//
//2. **Continuity:** Create continuous, unbroken lines that could be drawn by a mechanical rake or robotic arm in a single motion where possible.
//
//3. **Minimal Intersections:** Design patterns with minimal crossing lines, as is traditional in karesansui gardens.
//
//4. **Traditional Aesthetics:** Incorporate traditional karesansui design elements:
//   - Parallel ripple patterns (波紋, hamon) to represent water
//   - Straight lines to create clarity and structure
//   - Gentle curves to represent natural flow
//   - Asymmetrical balance (非対称の調和)
//
//5. **Technical Specifications:**
//   - Optimize for A4 portrait size (210mm × 297mm)
//   - Use a clear coordinate system with viewBox="0 0 210 297"
//   - Maintain proper scaling and proportions
//   - Ensure all lines have appropriate stroke width (typically 0.5-1.0px)
//   - Use only stroke paths (no fills)
//
//# OUTPUT FORMAT
//You must output ONLY the raw SVG code with no additional text, explanations, comments, markdown formatting, or code block markers.
//
//The SVG code must:
//- Start with the opening `< svg > ` tag including xmlns attribute and viewBox specification
//- End with the closing `</svg> ` tag
//- Be properly formatted and valid
//- Be immediately renderable in a web browser or vector editing software without any modifications
//
//# IMPORTANT
//Do not include any explanatory text, introductions, or conclusions.
//Do not wrap the SVG code in backticks or markdown code blocks.
//Return only the SVG code itself, nothing else.
//
//Example of expected output format:
//<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 210 297" width="210mm" height="297mm">
//  <path d="M10,10 C20,20 30,10 40,10" stroke="black" stroke-width="0.5" fill="none"/>
//  <!-- Additional paths as needed -->
//</svg>
//PROMPT
//                    ]
//                ]
//            ]]
//        ]);
//
//        if (!$response->ok()) {
//            return response()->json(['error' => 'Gemini API error', 'details' => $response->body()], 500);
//        }
//
//        // Gemini応答からSVG部分を取り出し
//        $result = $response->json();
//        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
//
////        $path = Storage::disk('s3')->putFile('haiku-image', $text);
////        $url = config('filesystems.disks.s3.url') . '/' . $path;
//
//        return response()->json([
//            'message' => 'Upload successful',
//            'svg' => $text,
////            'path' => $path,
////            'url' => $url,
//        ]);
//    }
}
