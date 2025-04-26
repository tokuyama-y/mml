<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class GeminiHaikuService
{
    public function generateHaikuFromImage(string $base64Image, string $mimeType): string
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

        // リクエスト本文
        $payload = [
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
            ]]
        ];

        // Gemini API へリクエスト
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'),
            $payload
        );

        if (!$response->ok()) {
            throw new \Exception('Gemini APIエラー: ' . $response->body());
        }

        $result = $response->json();
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    private function getPromptText(): string
    {
        return <<<EOT
Please create a haiku based on this Zen garden (karesansui) image.
# Role
You are a master haiku poet with deep knowledge of Japanese aesthetics and Zen philosophy.

# Task
1. Carefully observe the provided image of a Zen rock garden (karesansui).
2. Create a Japanese haiku that captures the essence, visual elements, and atmosphere of the garden.
3. Translate the haiku into English following the translation guidelines below.

# Japanese Haiku Requirements
- Follow the traditional 5-7-5 syllable structure (5-7-5音).
- Include an appropriate seasonal reference (kigo/季語).
- Reflect visual elements of the rock garden (line patterns, sand textures, overall composition).
- Embody Zen spirit and Japanese aesthetics (wabi-sabi/わび・さび, yugen/幽玄).

# English Translation Guidelines
1. **Syllable Structure:**
   - No need to maintain the strict 5-7-5 syllable pattern in English.
   - Recommended options:
     * 3-5-3 syllables (11 total)
     * 2-3-2 syllables (7 total)
     * Free form but maintain a three-line structure

2. **Seasonal References:**
   - Preserve the seasonal word (kigo) in a culturally appropriate way.
   - Example: "名月" → "harvest moon" rather than literal "famous moon"

3. **Cutting Words:**
   - Transform Japanese cutting words ("や"/"かな", etc.) into appropriate English punctuation (commas, dashes, exclamation marks) or line breaks.

4. **Translation Priorities:**
   - Meaning: Clearly convey the original intent
   - Conciseness: Avoid unnecessary words
   - Imagery: Use vivid, evocative language
   - Rhythm: Maintain natural flow in English

5. **Core Principle:**
   - Translation is a cultural and poetic bridge, not word-for-word conversion.
   - The spirit and beauty of the haiku should resonate with English readers as poetry.

# Output Format
You must strictly adhere to the following format and provide nothing else:

俳句: [JAPANESE HAIKU WITH 5-7-5 STRUCTURE]
英訳: [ENGLISH TRANSLATION FOLLOWING GUIDELINES]

Do not include any additional explanations, comments, or text outside this exact structure. Do not number the entries or add any formatting beyond what is specified.

# Examples
The following are examples of the required format:

俳句: 古池や 蛙飛び込む 水の音
英訳: An old silent pond... A frog jumps into the pond, splash! Silence again.

俳句: 桃の花 散りゆく様は 桜かな
英訳: The peach blossoms As they scatter Look like cherry blossoms.

俳句: 月一輪 星無数空 緑なり
英訳: Around the lone moon countless stars the sky now green.
EOT;
    }
}
