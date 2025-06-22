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
            'HTTP-Referer' => 'my-laravel-chatbot (your-email@example.com)',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'deepseek/deepseek-r1-0528:free',
                 //deepseek/deepseek-r1:free
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'You are a helpful assistant. Format responses with Markdown. Use headings (###), ' .
                                 'code blocks (```language), bullet points (-), and horizontal rules (---). ' .
                                 'For long responses, provide a summary first then details under collapsible sections. ' .
                                 'Keep code examples concise but complete.'
                ],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'max_tokens' => 800, // Increased for better formatting
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiReply = $data['choices'][0]['message']['content'] ?? 'No response from AI.';
            
            // Format and chunk long responses
            $aiReply = $this->formatResponse($aiReply);
            
            // If response is very long, split into chunks
            if (strlen($aiReply) > 1000) {
                $aiReply = $this->chunkLongResponse($aiReply);
            }
        } else {
            Log::error('OpenRouter Error: ' . $response->body());
            $aiReply = 'Error: ' . $response->status() . ' ' . $response->body();
        }

        return response()->json([
            'reply' => $aiReply,
        ]);
    }

    protected function formatResponse($content)
    {
        // Convert Markdown to HTML
        $content = preg_replace('/### (.*)/', '<h3>$1</h3>', $content);
        $content = preg_replace('/```(\w*)\n([\s\S]*?)\n```/', '<div class="code-block"><button class="copy-btn" title="Copy code"><i class="fas fa-copy"></i></button><pre><code class="language-$1">$2</code></pre></div>', $content);
        $content = preg_replace('/`([^`]+)`/', '<code>$1</code>', $content);
        $content = preg_replace('/---/', '<hr>', $content);
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        $content = preg_replace('/- (.*)/', '<li>$1</li>', $content);
        $content = preg_replace('/\n<li>/', '<ul><li>', $content);
        $content = str_replace('</li>\n<li>', '</li><li>', $content);
        $content = preg_replace('/(<\/li>)(\n)/', '$1</ul>$2', $content);
        
        // Convert line breaks to <br> tags
        $content = nl2br($content);
        
        return $content;
    }

    protected function chunkLongResponse($content)
    {
        // Split content at headings or code blocks
        $pattern = '/(<h3>.*?<\/h3>|<div class="code-block">.*?<\/div>|<hr>)/';
        $parts = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $chunks = [];
        $currentChunk = '';
        
        foreach ($parts as $part) {
            if (strlen($currentChunk) + strlen($part) > 800 && !empty($currentChunk)) {
                $chunks[] = $currentChunk;
                $currentChunk = '';
            }
            $currentChunk .= $part;
        }
        
        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }
        
        if (count($chunks) > 1) {
            $firstChunk = array_shift($chunks);
            $remainingContent = implode('', $chunks);
            return $firstChunk . '<div class="expandable-content" style="display:none;">' . $remainingContent . '</div>' .
                   '<button class="expand-btn">Show More <i class="fas fa-chevron-down"></i></button>';
        }
        
        return $content;
    }
}
