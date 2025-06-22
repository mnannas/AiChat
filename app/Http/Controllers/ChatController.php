<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function chat(Request $request)
    {
        $userMessage = $request->input('message');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'HTTP-Referer' => 'my-laravel-chatbot (your-email@example.com)', // IMPORTANT: put your real email here
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'deepseek/deepseek-r1:free',  // Put your selected model ID here
            // deepseek/deepseek-r1-0528:free
            // deepseek/deepseek-r1:free
            // tngtech/deepseek-r1t-chimera:free
            // nvidia/llama-3.3-nemotron-super-49b-v1:free
            // cognitivecomputations/dolphin3.0-r1-mistral-24b:free
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'max_tokens' => 500,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiReply = $data['choices'][0]['message']['content'] ?? 'No response from AI.';
        } else {
            Log::error('OpenRouter Error: ' . $response->body());
            $aiReply = 'Error: ' . $response->status() . ' ' . $response->body();
        }

        return response()->json([
            'reply' => $aiReply,
        ]);
    }
}
