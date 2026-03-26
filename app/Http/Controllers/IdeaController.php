<?php

namespace App\Http\Controllers;

use App\Agents\IdeaGeneratorAgent;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StreamableAgentResponse;

class IdeaController extends Controller
{
    public function generate(Request $request): StreamableAgentResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $agent = new IdeaGeneratorAgent;

        return $agent
            ->stream($validated['prompt'])
            ->usingVercelDataProtocol();
    }
}
