<?php

namespace {
    require $argv[1].'/vendor/autoload.php';
}

namespace App\Mcp\Tools {
    use Illuminate\Database\QueryException;
    use Illuminate\Support\Facades\DB;
    use Laravel\Mcp\Request;
    use Laravel\Mcp\Response;
    use Laravel\Mcp\Server\Tool;

    class ReadWorkspaceTool extends Tool
    {
        public function handle(Request $request): Response
        {
            $results = [];
            for ($attempt = 0; $attempt < 2; $attempt++) {
                try {
                    DB::statement('CREATE TABLE plugin_write_probe (id INTEGER)');
                    $results[] = 'wrote';
                } catch (QueryException $exception) {
                    $results[] = str_contains($exception->getMessage(), 'readonly') ? 'readonly' : $exception->getMessage();
                }
                DB::purge('sqlite');
            }

            return Response::json($results);
        }
    }
}

namespace {
    require $argv[2];
}
