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
     * using the Gemini API based on a text hint and/or a product image.
     */
    public function generateProductContent(Request $request): JsonResponse
    {
        $apiKey = config('services.gemini.api_key');
        $model  = config('services.gemini.model', 'gemini-2.0-flash');

        if (empty($apiKey)) {
            return response()->json(
                ['error' => 'GEMINI_API_KEY no configurat.'],
                503
            );
        }

        $request->validate([
            'hint'     => ['nullable', 'string', 'max:500'],
            'brand'    => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'image'    => ['nullable', 'image', 'max:8192'],
        ]);

        // Build context string
        $contextParts = [];
        if ($request->filled('hint'))     $contextParts[] = 'Product name / hint: ' . $request->hint;
        if ($request->filled('brand'))    $contextParts[] = 'Brand: ' . $request->brand;
        if ($request->filled('category')) $contextParts[] = 'Category: ' . $request->category;

        $context = implode("\n", $contextParts) ?: 'No extra information provided.';

        $prompt = <<<PROMPT
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
PROMPT;

        // ── Build Gemini content parts ───────────────────────────────────────
        $parts = [['text' => $prompt]];

        if ($request->hasFile('image')) {
            $file    = $request->file('image');
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $file->getMimeType(),
                    'data'     => base64_encode(file_get_contents($file->getRealPath())),
                ],
            ];
        }

        // ── Call Gemini API ──────────────────────────────────────────────────
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(30)->post($url, [
                'contents'         => [['parts' => $parts]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature'      => 0.7,
                    'maxOutputTokens'  => 2048,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Gemini API request failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'No s\'ha pogut connectar amb el servei d\'IA. Torna-ho a intentar.'], 503);
        }

        if (! $response->successful()) {
            $err = $response->json('error.message', 'Error del servei d\'IA (' . $response->status() . ').');
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['error' => $err], 502);
        }

        // ── Parse the JSON from the model's text output ──────────────────────
        $text = $response->json('candidates.0.content.parts.0.text', '');

        // Strip any accidental markdown fences
        $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/', '', $text);

        $generated = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($generated)) {
            Log::error('Gemini returned non-JSON output', ['raw' => $text]);
            return response()->json(['error' => 'L\'IA ha retornat un format inesperat. Torna-ho a intentar.'], 422);
        }

        return response()->json($generated);
    }
}
