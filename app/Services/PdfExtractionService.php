<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PdfExtractionService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.huggingface.url', env('HUGGINGFACE_SPACE_URL', 'http://127.0.0.1:5000')), '/');
        $this->token = config('services.huggingface.token', env('HF_TOKEN'));
    }

    /**
     * Send file to Hugging Face Space for OCR extraction.
     * Returns extracted data as array or null on failure.
     */
    public function extract(UploadedFile $file): ?array
    {
        try {
            $request = Http::timeout(60)
                ->connectTimeout(15);

            // Add Bearer token for private HF Spaces
            if ($this->token) {
                $request = $request->withHeaders([
                    'Authorization' => "Bearer {$this->token}",
                ]);
            }

            $response = $request
                ->attach('file', file_get_contents($file), $file->getClientOriginalName())
                ->post("{$this->baseUrl}/api/extract");

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    return $data['data'] ?? null;
                }

                // Return the message from the microservice for better UX
                Log::info('PDF extraction returned unsuccessful', [
                    'message' => $data['message'] ?? 'Unknown',
                    'method' => $data['method'] ?? 'N/A',
                ]);
            } else {
                Log::warning('PDF extraction request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PDF extraction service unavailable', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl,
            ]);

            return null;
        }
    }

    /**
     * Extract with detailed response (includes message on failure).
     * Returns full response array for the controller to handle.
     */
    public function extractWithDetails(UploadedFile $file): array
    {
        try {
            $request = Http::timeout(60)
                ->connectTimeout(15);

            if ($this->token) {
                $request = $request->withHeaders([
                    'Authorization' => "Bearer {$this->token}",
                ]);
            }

            $response = $request
                ->attach('file', file_get_contents($file), $file->getClientOriginalName())
                ->post("{$this->baseUrl}/api/extract");

            if ($response->successful()) {
                return $response->json();
            }

            // Try to parse error response from the microservice
            $body = $response->json();
            if ($body && isset($body['message'])) {
                return $body;
            }

            return [
                'success' => false,
                'message' => 'Service OCR mengembalikan error (HTTP ' . $response->status() . ').',
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'success' => false,
                'message' => 'Service OCR tidak dapat dihubungi. Pastikan Hugging Face Space aktif.',
            ];
        } catch (\Exception $e) {
            Log::error('PDF extraction error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi service OCR.',
            ];
        }
    }

    /**
     * Check if the OCR service is available.
     */
    public function isAvailable(): bool
    {
        try {
            $request = Http::timeout(10);

            if ($this->token) {
                $request = $request->withHeaders([
                    'Authorization' => "Bearer {$this->token}",
                ]);
            }

            $response = $request->get("{$this->baseUrl}/api/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}

