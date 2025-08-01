<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WorkflowDescriptionController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|min:10|max:1000',
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert en rédaction de documentation technique. Tu dois transformer la description donnée en une description plus détaillée et structurée en markdown.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->description
                    ]
                ],
                'temperature' => 0.0,
                'max_tokens' => 500
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Erreur lors de la communication avec OpenAI'
                ], 500);
            }

            $generatedDescription = $response->json('choices.0.message.content');

            return response()->json([
                'description' => $generatedDescription
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la génération de la description'
            ], 500);
        }
    }
}
