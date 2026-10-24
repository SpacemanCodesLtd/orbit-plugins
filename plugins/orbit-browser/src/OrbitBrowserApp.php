<?php

namespace OrbitBrowser;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\AppResource;
use Laravel\Mcp\Server\Attributes\AppMeta;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Uri;

#[Description('A read-only browser for sharing Orbit project context with chat.')]
#[AppMeta]
#[Uri('ui://orbit/browser-v0.3.5')]
class OrbitBrowserApp extends AppResource
{
    /**
     * Handle the app resource request.
     */
    public function handle(Request $request): Response
    {
        return Response::view('orbit-browser::browser', [
            'title' => $this->title(),
            'browserScript' => file_get_contents(dirname(__DIR__).'/assets/browser.js'),
            'dependencyScript' => file_get_contents(dirname(__DIR__).'/assets/dependency-check.mjs'),
            'icon' => file_get_contents(dirname(__DIR__).'/assets/icon.svg'),
        ]);
    }
}
