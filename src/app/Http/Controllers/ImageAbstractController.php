<?php

namespace App\Http\Controllers;

use App\Repositories\MindscapeResultRepository;
use App\Services\GeminiAbstractImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageAbstractController extends Controller
{
    protected $MindscapeResultRepository;
    protected $GeminiAbstractService;

    public function __construct(
        MindscapeResultRepository $MindscapeResult,
        GeminiAbstractImageService $GeminiAbstractService
    ) {
        $this->MindscapeResultRepository = $MindscapeResult;
        $this->GeminiAbstractService = $GeminiAbstractService;
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
        $url = config('filesystems.disks.s3.url') . '/' . $path;

        $MindscapeResult = $this->MindscapeResultRepository->create([
            'uploaded_image_url' => $url,
        ]);

        try {
            $svg = $this->GeminiAbstractService->generateSvgFromImage($base64, $mime);
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

        $MindscapeResult->mindscape_image_url = $mindscape_image_url;
        $MindscapeResult->save();

        try {
            $allCoordinates = $this->extractPathsFromSvg($svg);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Upload successful',
            'coordinates' => $allCoordinates,
        ]);
    }

    private function extractPathsFromSvg(string $svgContent): array
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadXML($svgContent);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('svg', 'http://www.w3.org/2000/svg');
        $paths = $xpath->query('//svg:path');
        $allCoordinates = [];

        foreach ($paths as $path) {
            /** @var \DOMElement $path */
            $d = $path->getAttribute('d');
            Log::debug("$d:");
            $coords = $this->extractCoordinatesFromPath($d);
            $allCoordinates = array_merge($allCoordinates, $coords);
        }

        return $allCoordinates;
    }

    private function extractCoordinatesFromPath(string $d): array
    {
        $d = preg_replace('/([MLZ])/i', ' $1 ', $d);
        $d = str_replace(',', ' ', $d);
        $d = preg_replace('/\s+/', ' ', trim($d));

        $tokens = preg_split('/\s+/', $d);
        $coords = [];
        $currentCommand = null;

        for ($i = 0; $i < count($tokens); $i++) {
            $token = strtoupper($tokens[$i]);

            if (in_array($token, ['M', 'L'])) {
                $currentCommand = $token;
                continue;
            }

            if ($currentCommand && is_numeric($token)) {
                $x = floatval($token);
                $y = isset($tokens[$i + 1]) ? floatval($tokens[++$i]) : null;

                if ($y !== null) {
                    $coords[] = [$x, $y];
                }
            }

            if ($token === 'Z' && !empty($coords)) {
                $coords[] = $coords[0];
            }
        }

        return $coords;
    }
}
