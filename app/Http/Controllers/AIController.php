<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function generateText($prompt)
    {
        $response = Http::post($this->apiUrl . '?key=' . env('GEMINI_API_KEY'), [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => 'Gemini API failed: ' . $response->body()];
    }

    public function generateDIY(Request $request)
    {
        $prompt = $request->input('prompt');
        $imageDescription = $request->input('image_description'); // optional

        if (!$prompt && !$imageDescription) {
            return response()->json(['error' => 'Prompt or image description is required'], 400);
        }

        $finalPrompt = "Suggest a short DIY project based on this idea: " . $prompt;

        if ($imageDescription) {
            $finalPrompt .= " The image shows: " . $imageDescription;
        }

        $finalPrompt .= ". Keep the response short and include a recommended YouTube video link.";

        $result = $this->generateText($finalPrompt);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 500);
        }

        $fullText = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

        preg_match('/https:\/\/www\.youtube\.com\/watch\?v=[\w-]+/', $fullText, $matches);
        $videoUrl = $matches[0] ?? 'https://www.youtube.com/results?search_query=' . urlencode($prompt);

        $description = strlen($fullText) > 300 ? substr($fullText, 0, 300) . '...' : $fullText;

        return response()->json([
            'title' => 'DIY Project',
            'description' => $description,
            'videoUrl' => $videoUrl,
            'image' => 'https://via.placeholder.com/300x200'
        ]);
    }
}


