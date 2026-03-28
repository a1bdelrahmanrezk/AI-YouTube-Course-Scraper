<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{

    public function generateCourseTitles(string $category): array
    {
        $apiKey = config('services.ai_agent.key');
        $numberOfGeneratedTitles = rand(config('services.ai_agent.min_number_of_generated_titles'), config('services.ai_agent.max_number_of_generated_titles'));

        Log::info("AI-SERVICE: Starting title generation for category: {$category}");

        $models = [
            'google/gemma-3-4b-it:free',
            'meta-llama/llama-3.1-8b-instruct:free',
            'microsoft/phi-3-medium-128k-instruct:free'
        ];

        foreach ($models as $model) {
            Log::info("AI-SERVICE: Trying model: {$model}");

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'X-Title' => 'YouTube Scraper App',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Generate {$numberOfGeneratedTitles} educational course titles about {$category}. Return as a clean list, each title on a new line, without numbering."
                    ]
                ]
            ]);

            Log::info("AI-SERVICE: Response status: " . $response->status());

            if ($response->successful()) {
                $responseData = $response->json();
                $text = $responseData['choices'][0]['message']['content'] ?? '';
                Log::info('AI-SERVICE: Extracted text: ' . $text);
                return $this->cleanTitles($text);
            } elseif ($response->status() === 429) {
                Log::warning("AI-SERVICE: Model {$model} rate limited, trying next...");
                continue;
            } else {
                Log::error("AI-SERVICE: Model {$model} failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                continue;
            }
        }

        Log::error("AI-SERVICE: All models failed for category: {$category}");
        return [];
    }

    private function cleanTitles(string $text): array
    {
        return collect(explode("\n", $text))
            ->map(fn($line) => trim(preg_replace('/^\d+[\.\-\)]\s*/', '', $line)))
            ->filter()
            ->values()
            ->toArray();
    }
}
