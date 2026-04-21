<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminAiController extends Controller
{
    /**
     * Generate SEO-friendly product content (name, descriptions, meta fields)
     * using the Claude API based on a text hint and/or a product image.
     */
    public function generateProductContent(Request $request): JsonResponse
    {
        $apiKey = config('services.anthropic.key');
        $model  = config('services.anthropic.model', 'claude-haiku-4-5-20251001');

        if (empty($apiKey)) {
            return response()->json(
                ['error' => 'ANTHROPIC_API_KEY is not configured.'],
                503
            );
        }

        $request->validate([
            'hint'          => ['nullable', 'string', 'max:500'],
            'brand'         => ['nullable', 'string', 'max:100'],
            'category'      => ['nullable', 'string', 'max:100'],
            'image'         => ['nullable', 'image', 'max:8192'],
        ]);

        // ── Build the message content blocks ────────────────────────────────
        $content = [];

        // Attach image if provided
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $mime      = $file->getMimeType();
            $b64       = base64_encode(file_get_contents($file->getRealPath()));

            $content[] = [
                'type'   => 'image',
                'source' => [
                    'type'       => 'base64',
                    'media_type' => $mime,
                    'data'       => $b64,
                ],
            ];
        }

        // Build context string
        $contextParts = [];
        if ($request->filled('hint'))     $contextParts[] = 'Product name / hint: ' . $request->hint;
        if ($request->filled('brand'))    $contextParts[] = 'Brand: ' . $request->brand;
        if ($request->filled('category')) $contextParts[] = 'Category: ' . $request->category;

        $context = implode("\n", $contextParts) ?: 'No extra information provided.';

        $content[] = [
            'type' => 'text',
            'text' => <<<PROMPT
You are an expert product copywriter for "Copyus", a B2B print supplies and printing services company.
Your task is to generate complete product content in three languages: Catalan (ca), Spanish (es), and English (en).

Product information:
{$context}

Generate a JSON object with EXACTLY this structure (no extra keys, no markdown, no explanation):
{
  "name": {
    "ca": "Product name in Catalan (max 80 chars)",
    "es": "Product name in Spanish (max 80 chars)",
    "en": "Product name in English (max 80 chars)"
  },
  "short_description": {
    "ca": "One-sentence benefit-focused description in Catalan (max 120 chars)",
    "es": "One-sentence benefit-focused description in Spanish (max 120 chars)",
    "en": "One-sentence benefit-focused description in English (max 120 chars)"
  },
  "description": {
    "ca": "3-4 sentence professional B2B description in Catalan",
    "es": "3-4 sentence professional B2B description in Spanish",
    "en": "3-4 sentence professional B2B description in English"
  },
  "meta_title": {
    "ca": "SEO title in Catalan (max 60 chars, include brand if relevant)",
    "es": "SEO title in Spanish (max 60 chars)",
    "en": "SEO title in English (max 60 chars)"
  },
  "meta_description": {
    "ca": "SEO meta description in Catalan (max 155 chars, compelling, action-oriented)",
    "es": "SEO meta description in Spanish (max 155 chars)",
    "en": "SEO meta description in English (max 155 chars)"
  },
  "meta_keywords": {
    "ca": "keyword1, keyword2, keyword3, keyword4, keyword5 (Catalan, 5-8 keywords)",
    "es": "keyword1, keyword2, keyword3, keyword4, keyword5 (Spanish, 5-8 keywords)",
    "en": "keyword1, keyword2, keyword3, keyword4, keyword5 (English, 5-8 keywords)"
  }
}

Rules:
- Catalan (ca) is the primary language — use natural Catalan, not Spanish translated.
- Tone: professional, B2B, concise.
- Return ONLY the raw JSON object. No markdown fences, no explanation, no extra text.
PROMPT
        ];

        // ── Call Anthropic Messages API ──────────────────────────────────────
        try {
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $model,
                'max_tokens' => 2048,
                'messages'   => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Anthropic API request failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Could not reach the AI service. Please try again.'], 503);
        }

        if (!$response->successful()) {
            Log::error('Anthropic API error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['error' => 'AI service returned an error. Please try again.'], 502);
        }

        // ── Parse the JSON from the model's text output ──────────────────────
        $text = $response->json('content.0.text', '');
        // Strip any accidental markdown fences
        $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/', '', $text);

        $generated = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($generated)) {
            Log::error('AI returned non-JSON output', ['raw' => $text]);
            return response()->json(['error' => 'AI returned unexpected output. Please try again.'], 422);
        }

        return response()->json($generated);
    }
}
