<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeleteApiResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:api-resource {name : The name of the resource} {--api-version=1 : API version number} {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all files created by make:api-resource command';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $version = $this->option('api-version');
        $versionPath = "V{$version}";

        $this->warn("🗑️  Preparing to delete API resource: {$name} (Version {$version})");
        $this->newLine();

        // Build list of files to delete
        $filesToDelete = $this->getFilesToDelete($name, $versionPath);

        if (empty($filesToDelete['existing'])) {
            $this->info('No files found to delete.');
            return Command::SUCCESS;
        }

        // Show files that will be deleted
        $this->comment('📋 Files to be deleted:');
        foreach ($filesToDelete['existing'] as $file) {
            $this->line("  ✗ {$file}");
        }

        if (!empty($filesToDelete['missing'])) {
            $this->newLine();
            $this->comment('ℹ️  Files not found (already deleted):');
            foreach ($filesToDelete['missing'] as $file) {
                $this->line("  - {$file}");
            }
        }

        $this->newLine();

        // Confirm deletion
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete these files?', false)) {
                $this->info('Deletion cancelled.');
                return Command::SUCCESS;
            }
        }

        // Delete files
        $deletedCount = 0;
        foreach ($filesToDelete['existing'] as $file) {
            if (File::delete($file)) {
                $deletedCount++;
                $this->comment("Deleted: {$file}");
            }
        }

        // Delete empty directories
        $this->deleteEmptyDirectories($name, $versionPath);

        $this->newLine();
        $this->info("✅ Successfully deleted {$deletedCount} file(s)!");

        return Command::SUCCESS;
    }

    /**
     * Get list of files to delete.
     */
    private function getFilesToDelete(string $name, string $versionPath): array
    {
        $basePath = base_path();
        $existing = [];
        $missing = [];

        $potentialFiles = [
            // Model
            "{$basePath}/app/Models/{$name}/{$name}.php",
            
            // Controller
            "{$basePath}/app/Http/Controllers/Api/{$versionPath}/{$name}/{$name}Controller.php",
            
            // Requests
            "{$basePath}/app/Http/Requests/Api/{$versionPath}/{$name}/Store{$name}Request.php",
            "{$basePath}/app/Http/Requests/Api/{$versionPath}/{$name}/Update{$name}Request.php",
            
            // Resources
            "{$basePath}/app/Http/Resources/{$name}/{$name}Resource.php",
            "{$basePath}/app/Http/Resources/{$name}/{$name}Collection.php",
            
            // Test
            "{$basePath}/tests/Feature/Api/{$versionPath}/{$name}/{$name}Test.php",
            
            // Seeder
            "{$basePath}/database/seeders/{$name}/{$name}Seeder.php",
            
            // Factory
            "{$basePath}/database/factories/{$name}/{$name}Factory.php",
        ];

        // Find migration files (they have timestamps)
        $migrationPattern = database_path("migrations/*_create_{$this->pluralize($name)}_table.php");
        $migrations = glob($migrationPattern);
        if (!empty($migrations)) {
            $potentialFiles = array_merge($potentialFiles, $migrations);
        }

        foreach ($potentialFiles as $file) {
            if (File::exists($file)) {
                $existing[] = $file;
            } else {
                $missing[] = $file;
            }
        }

        return [
            'existing' => $existing,
            'missing' => $missing,
        ];
    }

    /**
     * Delete empty directories after removing files.
     */
    private function deleteEmptyDirectories(string $name, string $versionPath): void
    {
        $basePath = base_path();
        
        $directories = [
            "{$basePath}/app/Models/{$name}",
            "{$basePath}/app/Http/Controllers/Api/{$versionPath}/{$name}",
            "{$basePath}/app/Http/Requests/Api/{$versionPath}/{$name}",
            "{$basePath}/app/Http/Resources/{$name}",
            "{$basePath}/tests/Feature/Api/{$versionPath}/{$name}",
            "{$basePath}/database/seeders/{$name}",
            "{$basePath}/database/factories/{$name}",
        ];

        foreach ($directories as $dir) {
            if (File::isDirectory($dir) && $this->isDirectoryEmpty($dir)) {
                File::deleteDirectory($dir);
                $this->comment("Deleted empty directory: {$dir}");
            }
        }

        // Check and delete version directories if empty
        $versionDirs = [
            "{$basePath}/app/Http/Controllers/Api/{$versionPath}",
            "{$basePath}/app/Http/Requests/Api/{$versionPath}",
            "{$basePath}/tests/Feature/Api/{$versionPath}",
        ];

        foreach ($versionDirs as $dir) {
            if (File::isDirectory($dir) && $this->isDirectoryEmpty($dir)) {
                File::deleteDirectory($dir);
                $this->comment("Deleted empty version directory: {$dir}");
            }
        }
    }

    /**
     * Check if directory is empty.
     */
    private function isDirectoryEmpty(string $dir): bool
    {
        if (!File::isDirectory($dir)) {
            return false;
        }

        $files = File::files($dir);
        $directories = File::directories($dir);

        return empty($files) && empty($directories);
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
