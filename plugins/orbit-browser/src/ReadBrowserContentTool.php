<?php

namespace OrbitBrowser;

use App\Mcp\Tools\ReadProjectTool;
use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Read Orbit project tasks, scratchpad, documents and links for the browser, or saved dependency inspection results. Never runs checks or returns secrets.')]
#[IsReadOnly]
class ReadBrowserContentTool extends Tool
{
    public function handle(Request $request, ReadProjectTool $reader): Response
    {
        $data = $request->validate([
            'project_id' => ['required', 'uuid'],
            'section' => ['sometimes', 'in:project,inspection'],
        ]);
        $response = $reader->handle(new Request($data));
        if (($data['section'] ?? 'project') === 'inspection') {
            return $response;
        }
        $project = array_intersect_key(json_decode((string) $response->content(), true, flags: JSON_THROW_ON_ERROR), array_flip([
            'id', 'name', 'status', 'description', 'revision', 'scratchpad', 'documents', 'document_folders', 'board', 'links',
        ]));
        $project['scratchpad_html'] = Task::renderDescription($project['scratchpad'] ?? '');
        foreach ($project['documents'] as &$document) {
            $document['body_html'] = Task::renderDescription($document['body'] ?? '');
        }
        unset($document);
        foreach ($project['board'] as &$column) {
            foreach ($column['tasks'] as &$task) {
                $task['description_html'] = Task::renderDescription($task['description'] ?? '');
            }
            unset($task);
        }
        unset($column);

        return Response::json($project);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->string()->description('Orbit project ID from read_workspace')->required(),
            'section' => $schema->string()->enum(['project', 'inspection'])->description('Defaults to project. Inspection returns saved folder/package results.'),
        ];
    }
}
