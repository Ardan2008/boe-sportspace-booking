<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KtpOcrService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.ocr.api_key', 'free');
        $this->endpoint = config('services.ocr.endpoint', 'https://api.ocr.space/parse/image');
    }

    public function verifyName(string $imagePath, string $expectedName): array
    {
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            return ['valid' => false, 'message' => 'File KTP tidak ditemukan.'];
        }

        $extractedText = $this->extractText($fullPath);

        if ($extractedText === null) {
            return ['valid' => false, 'message' => 'Gagal memproses OCR. Server OCR tidak merespons.'];
        }

        if (empty(trim($extractedText))) {
            return ['valid' => false, 'message' => 'Tidak dapat membaca teks dari foto KTP. Pastikan foto KTP jelas dan terbaca.'];
        }

        $normalizedText = $this->normalizeText($extractedText);
        $normalizedName = $this->normalizeText($expectedName);

        if (empty($normalizedName)) {
            return ['valid' => false, 'message' => 'Nama tidak valid.'];
        }

        $nameWords = preg_split('/\s+/', $normalizedName);
        $nameWords = array_filter($nameWords, fn($w) => mb_strlen($w) >= 2);
        $nameWords = array_values($nameWords);

        if (empty($nameWords)) {
            return ['valid' => false, 'message' => 'Nama terlalu pendek.'];
        }

        $textWords = preg_split('/\s+/', $normalizedText);
        $textStr = implode(' ', $textWords);

        $foundCount = 0;
        foreach ($nameWords as $word) {
            if (str_contains($textStr, $word)) {
                $foundCount++;
            }
        }

        $threshold = ceil(count($nameWords) * 0.7);

        if ($foundCount >= $threshold) {
            return ['valid' => true, 'message' => 'Nama sesuai dengan KTP.'];
        }

        $foundPercent = round(($foundCount / count($nameWords)) * 100);
        Log::info("KTP OCR mismatch: expected=\"{$normalizedName}\", found={$foundCount}/" . count($nameWords) . " words, text=\"{$textStr}\"");

        return [
            'valid' => false,
            'message' => "Nama \"{$expectedName}\" tidak sesuai dengan nama pada foto KTP. Harap upload foto KTP yang jelas dan masukkan nama sesuai KTP."
        ];
    }

    protected function extractText(string $filePath): ?string
    {
        try {
            $response = Http::timeout(30)->asMultipart()->post($this->endpoint, [
                ['name' => 'apikey', 'contents' => $this->apiKey],
                ['name' => 'language', 'contents' => 'ind'],
                ['name' => 'isOverlayRequired', 'contents' => 'false'],
                ['name' => 'OCREngine', 'contents' => '2'],
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => 'ktp.jpg',
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('OCR.space API returned status ' . $response->status());
                return null;
            }

            $data = $response->json();

            if (!($data['ParsedResults'][0]['ParsedText'] ?? null)) {
                $errorMsg = $data['ErrorMessage'] ?? 'Unknown OCR error';
                Log::warning('OCR.space parse failed: ' . $errorMsg);
                return null;
            }

            return $data['ParsedResults'][0]['ParsedText'];
        } catch (\Exception $e) {
            Log::error('OCR.space request failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function normalizeText(string $text): string
    {
        $text = preg_replace('/[^a-zA-Z\s.\x27\x2D]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim(mb_strtolower($text));
    }
}
