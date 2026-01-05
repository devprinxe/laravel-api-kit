<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeApiResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-resource {name : The name of the resource} {--api-version=1 : API version number} {--skip-model} {--skip-controller} {--skip-requests} {--skip-resources} {--skip-test} {--skip-seeder} {--skip-migration} {--skip-factory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a complete API resource with model, controller, requests, resources, test, and seeder';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $version = $this->option('api-version');
        $versionPath = "V{$version}";
        $namePath = "{$name}/{$name}";

        $this->info("🚀 Creating API resource: {$name} (Version {$version})");
        $this->newLine();

        $commands = [];

        // 1. Model with migration and factory
        if (!$this->option('skip-model')) {
            $modelFlags = [];
            if (!$this->option('skip-migration')) {
                $modelFlags[] = 'm';
            }
            if (!$this->option('skip-factory')) {
                $modelFlags[] = 'f';
            }

            if (!empty($modelFlags)) {
                $flags = '-' . implode('', $modelFlags);
                $commands[] = [
                    'command' => "make:model {$namePath} {$flags}",
                    'description' => 'Model' . 
                        (!$this->option('skip-migration') ? ' + Migration' : '') . 
                        (!$this->option('skip-factory') ? ' + Factory' : ''),
                ];
            } else {
                $commands[] = [
                    'command' => "make:model {$namePath}",
                    'description' => 'Model',
                ];
            }
        }

        // 2. API Controller
        if (!$this->option('skip-controller')) {
            $commands[] = [
                'command' => "make:controller Api/{$versionPath}/{$name}/{$name}Controller --api",
                'description' => 'API Controller',
            ];
        }

        // 3. Form Requests
        if (!$this->option('skip-requests')) {
            $commands[] = [
                'command' => "make:request Api/{$versionPath}/{$name}/Store{$name}Request",
                'description' => 'Store Request',
            ];
            $commands[] = [
                'command' => "make:request Api/{$versionPath}/{$name}/Update{$name}Request",
                'description' => 'Update Request',
            ];
        }

        // 4. API Resources
        if (!$this->option('skip-resources')) {
            $commands[] = [
                'command' => "make:resource {$name}/{$name}Resource",
                'description' => 'API Resource',
            ];
            $commands[] = [
                'command' => "make:resource {$name}/{$name}Collection",
                'description' => 'API Collection',
            ];
        }

        // 5. Feature Test
        if (!$this->option('skip-test')) {
            $commands[] = [
                'command' => "make:test Api/{$versionPath}/{$name}/{$name}Test --pest",
                'description' => 'Pest Test',
            ];
        }

        // 6. Seeder
        if (!$this->option('skip-seeder')) {
            $commands[] = [
                'command' => "make:seeder {$name}/{$name}Seeder",
                'description' => 'Seeder',
            ];
        }

        // Execute commands
        foreach ($commands as $cmd) {
            $this->comment("Creating {$cmd['description']}...");
            Artisan::call($cmd['command']);
        }

        $this->newLine();
        $this->info('✅ API resource created successfully!');
        $this->newLine();

        // Summary
        $this->comment('📋 Created files:');
        foreach ($commands as $cmd) {
            $this->line("  ✓ {$cmd['description']}");
        }

        $this->newLine();
        $this->comment('💡 Next steps:');
        $this->line("  1. Define migration fields in: database/migrations/*_create_{$this->pluralize($name)}_table.php");
        $this->line("  2. Add routes in: routes/api.php");
        $this->line("  3. Implement controller logic in: app/Http/Controllers/Api/{$versionPath}/{$name}/{$name}Controller.php");
        $this->line("  4. Configure validation in: app/Http/Requests/Api/{$versionPath}/{$name}/Store{$name}Request.php");
        $this->line("  5. Run: php artisan migrate");

        return Command::SUCCESS;
    }

    /**
     * Pluralize the resource name for migration files.
     */
    private function pluralize(string $name): string
    {
        $baseName = Str::studly(class_basename(str_replace(['/', '\\'], '\\', $name)));

        return Str::snake(Str::pluralStudly($baseName));
    }
}
