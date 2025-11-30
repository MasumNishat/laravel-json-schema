<?php

namespace Masum\JsonSchema\Console;

use Illuminate\Console\Command;
use Masum\JsonSchema\Schema;

class ValidateDataCommand extends Command
{
    protected $signature = 'schema:validate {schema} {data?} {--file=}';
    protected $description = 'Validate data against a JSON Schema';

    public function handle(): int
    {
        $schemaName = $this->argument('schema');
        $data = $this->argument('data');
        $file = $this->option('file');

        // Load schema
        $schema = $this->loadSchema($schemaName);

        if (!$schema) {
            $this->error("Schema {$schemaName} not found!");
            return self::FAILURE;
        }

        // Load data
        $data = $this->loadData($data, $file);

        if (!$data) {
            $this->error('No data provided!');
            return self::FAILURE;
        }

        // Validate
        $result = Schema::validate($data, $schema);

        if ($result['valid']) {
            $this->info('✅ Data is valid against the schema!');
            return self::SUCCESS;
        }

        $this->error('❌ Data validation failed:');
        foreach ($result['errors'] as $error) {
            $this->line("  - {$error}");
        }

        return self::FAILURE;
    }

    protected function loadSchema(string $name)
    {
        $path = config('json-schema.storage.path', storage_path('app/json-schemas'));
        $schemaFile = $path . '/' . $name . '.php';

        if (!file_exists($schemaFile)) {
            return null;
        }

        return require $schemaFile;
    }

    protected function loadData($data, $file)
    {
        if ($file) {
            if (!file_exists($file)) {
                $this->error("File {$file} not found!");
                return null;
            }

            $content = file_get_contents($file);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON in file!');
                return null;
            }

            return $data;
        }

        if ($data) {
            $decoded = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            // If not JSON, treat as string data
            return $data;
        }

        // Try to read from stdin
        $stdin = file_get_contents('php://stdin');

        if (!empty($stdin)) {
            $decoded = json_decode($stdin, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $stdin;
        }

        return null;
    }
}