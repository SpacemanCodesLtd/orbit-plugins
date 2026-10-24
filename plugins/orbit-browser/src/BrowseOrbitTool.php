<?php

namespace OrbitBrowser;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\RendersApp;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Title('Orbit')]
#[Description('Browse Orbit projects and share tasks, scratchpad, documents, dependency findings and links.')]
#[IsReadOnly]
#[RendersApp(OrbitBrowserApp::class)]
class BrowseOrbitTool extends Tool
{
    /**
     * @var array<string, mixed>
     */
    protected ?array $meta = ['openai/ui' => ['entrypoints' => [['type' => 'global']]]];

    public function handle(Request $request): Response
    {
        return Response::text('Browse projects, select project content, and share it with the chat from Orbit.');
    }
}
