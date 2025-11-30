<?php

namespace Masum\JsonSchema\Console;

use Illuminate\Console\Command;
use Masum\JsonSchema\Schema;

class MakeSchemaCommand extends Command
{
    protected $signature = 'make:schema {name} {--example}';
    protected $description = 'Create a new JSON Schema class';

    public function handle(): int
    {
        $name = $this->argument('name');
        $example = $this->option('example');

        $stub = $example ? 'schema.example.stub' : 'schema.stub';
        $path = $this->getPath($name);

        $this->ensureDirectoryExists($path);

        if (file_exists($path)) {
            $this->error("Schema {$name} already exists!");
            return self::FAILURE;
        }

        file_put_contents($path, $this->buildClass($name, $stub));

        $this->info("Schema {$name} created successfully.");

        if ($example) {
            $this->info('Example schema created. Check the file for usage examples.');
        }

        return self::SUCCESS;
    }

    protected function getPath(string $name): string
    {
        $storagePath = config('json-schema.storage.path', storage_path('app/json-schemas'));
        return $storagePath . '/' . $name . '.php';
    }

    protected function ensureDirectoryExists(string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function buildClass(string $name, string $stub): string
    {
        $stubPath = __DIR__ . '/stubs/' . $stub;

        if (!file_exists($stubPath)) {
            return $this->getDefaultStub($name);
        }

        $content = file_get_contents($stubPath);
        return str_replace('{{name}}', $name, $content);
    }

    protected function getDefaultStub(string $name): string
    {
        return <<<PHP
<?php

/**
 * JSON Schema: {$name}
 * 
 * This schema defines the structure for {$name} data validation.
 */

use Masum\\JsonSchema\\Schema;

return Schema::object()
    ->property('id', Schema::string()->format('uuid'))
    ->property('name', Schema::string()->minLength(1)->maxLength(255))
    ->property('created_at', Schema::string()->format('date-time'))
    ->required(['id', 'name', 'created_at']);

PHP;
    }
}