<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class GeminiAbstractImageService
{
    public function generateSvgFromImage(string $base64Image, string $mimeType): string
    {
        $scopes = [
            'https://www.googleapis.com/auth/generative-language'
        ];

        // OAuth 2.0 トークン取得
        $credentials = new ServiceAccountCredentials(
            $scopes,
            config('services.google.credentials_path')
        );
        $tokenData = $credentials->fetchAuthToken();

        if (!is_array($tokenData) || !isset($tokenData['access_token'])) {
            throw new \Exception("アクセストークンの取得に失敗しました");
        }

        $accessToken = $tokenData['access_token'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [[
                'parts' => [
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $base64Image,
                        ]
                    ],
                    [
                        'text' => $this->getPromptText(),
                    ]
                ]
            ]],
            'safetySettings' => [
                [
                    'category' => "HARM_CATEGORY_HATE_SPEECH",
                    'threshold' => "BLOCK_ONLY_HIGH",
                ],
                [
                    'category' => "HARM_CATEGORY_SEXUALLY_EXPLICIT",
                    'threshold' => "BLOCK_ONLY_HIGH",
                ],
                [
                    'category' => "HARM_CATEGORY_DANGEROUS_CONTENT",
                    'threshold' => "BLOCK_ONLY_HIGH",
                ],
                [
                    'category' => "HARM_CATEGORY_HARASSMENT",
                    'threshold' => "BLOCK_ONLY_HIGH",
                ],
            ],
        ]);

        if (!$response->ok()) {
            throw new \Exception('Gemini APIエラー: ' . $response->body());
        }

        $result = $response->json();
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    private function getPromptText(): string
    {
        return <<<EOT
Abstract the impression of this image and convert it into geometric patterns resembling a mental landscape, then generate SVG code

# ROLE
You are an expert at abstracting the impression of images and converting them into geometric patterns resembling mental landscapes, and you are proficient in generating SVG code.

# TASK
Abstract the provided image, convert it into geometric patterns resembling a mental landscape, and generate SVG code.

# TECHNICAL REQUIREMENTS
1. **Simplification:** Extract and simplify the key features and contours from the image while maintaining its essential character.

2. **Continuity:** Create continuous, unbroken lines that could be drawn by a mechanical rake or robotic arm in a single motion where possible.

3. **Only geometric shapes:** Only geometric shapes (e.g., circles, triangles, straight lines).

4. **Traditional Aesthetics:** Incorporate traditional karesansui design elements:
   - Parallel ripple patterns (波紋, hamon) to represent water
   - Straight lines to create clarity and structure
   - Gentle curves to represent natural flow
   - Asymmetrical balance (非対称の調和)
   - Do not include text

5. **Technical Specifications:**
   - Optimized for a vertical size of 226 mm × 240 mm
   - Use a clear coordinate system with viewBox="0 0 226 240"
   - Maintain proper scaling and proportions
   - Ensure all lines have appropriate stroke width (typically 0.5-1.0px)
   - Use only stroke paths (no fills)

# OUTPUT FORMAT
You must output ONLY the raw SVG code with no additional text, explanations, comments, markdown formatting, or code block markers(
svg
).

The SVG code must:
- Start with the opening < svg >  tag including xmlns attribute and viewBox specification
- End with the closing </svg>  tag
- Be properly formatted and valid
- Be immediately renderable in a web browser or vector editing software without any modifications


# IMPORTANT
Do not include any explanatory text, introductions, or conclusions.
Do not wrap the SVG code in backticks or markdown code blocks.
Return only the SVG code itself, nothing else.

Example of expected output format:
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 210 297" width="210mm" height="297mm">
  <path d="M10,10 C20,20 30,10 40,10" stroke="black" stroke-width="0.5" fill="none"/>
  <!-- Additional paths as needed -->
</svg>
EOT;
    }
}
