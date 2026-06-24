<?php

namespace Modules\AI\AIProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\AI\app\Contracts\AIProviderInterface;
use Modules\AI\app\Exceptions\AIProviderException;
use Modules\AI\app\Exceptions\ApiException;

class GeminiProvider implements AIProviderInterface
{
    protected string $apiKey;
    protected string $endpoint;
    protected int $timeout;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key') ?? '';
        $this->endpoint = config('gemini.endpoint') ?? 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $this->timeout = (int) (config('gemini.request_timeout') ?? 10);
        $this->model = config('gemini.model') ?? 'gemini-2.0-flash';
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function setOrganization(?string $organization): self
    {
        // Gemini doesn't use organization ID, but keeping for interface compatibility
        return $this;
    }

    public function getName(): string
    {
        return 'gemini';
    }

    /**
     * Generate content using Gemini API
     *
     * @param string $prompt The prompt to send to Gemini
     * @param string|null $imageUrl Optional image URL for multimodal requests
     * @param array $options Additional options
     * @return string The generated response
     * @throws AIProviderException
     */
    public function generate(string $prompt, ?string $imageUrl = null, array $options = []): string
    {
        if (empty($this->apiKey)) {
            throw new AIProviderException('Gemini API key is not configured.');
        }

        try {
            $contents = $this->buildContents($prompt, $imageUrl);

            $requestBody = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'topK' => $options['top_k'] ?? 40,
                    'topP' => $options['top_p'] ?? 0.95,
                    'maxOutputTokens' => $options['max_tokens'] ?? 2048,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                ]
            ];

            $url = $this->endpoint . '?key=' . $this->apiKey;

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $requestBody);

            if (!$response->successful()) {
                $error = $response->json('error.message') ?? 'Unknown error';
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'error' => $error,
                    'prompt_length' => strlen($prompt)
                ]);
                throw new AIProviderException("Gemini API error: {$error}");
            }

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Gemini API: Unexpected response structure', ['response' => $data]);
                throw new AIProviderException('Invalid response structure from Gemini API');
            }

            return $data['candidates'][0]['content']['parts'][0]['text'];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini API Connection Error', ['message' => $e->getMessage()]);
            throw new AIProviderException('Failed to connect to Gemini API: ' . $e->getMessage());
        }
    }

    /**
     * Build the contents array for the API request
     */
    protected function buildContents(string $prompt, ?string $imageUrl = null): array
    {
        $parts = [];

        // Add image if provided (multimodal)
        if ($imageUrl) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $this->getMimeType($imageUrl),
                    'data' => $this->getImageBase64($imageUrl)
                ]
            ];
        }

        // Add text prompt
        $parts[] = ['text' => $prompt];

        return [
            [
                'role' => 'user',
                'parts' => $parts
            ]
        ];
    }

    /**
     * Get MIME type from image URL
     */
    protected function getMimeType(string $imageUrl): string
    {
        $extension = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

        return match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg'
        };
    }

    /**
     * Get base64 encoded image data
     */
    protected function getImageBase64(string $imageUrl): string
    {
        try {
            $imageData = Http::timeout(5)->get($imageUrl)->body();
            return base64_encode($imageData);
        } catch (\Exception $e) {
            Log::warning('Failed to fetch image for Gemini', ['url' => $imageUrl, 'error' => $e->getMessage()]);
            throw new AIProviderException('Failed to process image: ' . $e->getMessage());
        }
    }

    /**
     * Generate structured JSON response from Gemini
     * Optimized for search intent extraction
     */
    public function generateJson(string $prompt, array $options = []): ?array
    {
        $jsonPrompt = $prompt . "\n\nIMPORTANT: Respond with valid JSON only. No markdown, no explanation, just the JSON object.";

        $response = $this->generate($jsonPrompt, null, array_merge($options, [
            'temperature' => 0.3, // Lower temperature for more consistent JSON
        ]));

        // Clean the response - remove any markdown code blocks
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*/', '', $response);
        $response = trim($response);

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Gemini returned invalid JSON', [
                'response' => substr($response, 0, 500),
                'error' => json_last_error_msg()
            ]);
            return null;
        }

        return $decoded;
    }

    /**
     * Check if the provider is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
