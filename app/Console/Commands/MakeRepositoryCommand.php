<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (File::exists($path)) {
            $this->error('Repository already exists!');
            return;
        }

        $stub = <<<PHP
        <?php

        namespace App\Repositories;

        class {$name}
        {
            //
        }

        PHP;

        File::ensureDirectoryExists(app_path('Repositories'));
        File::put($path, $stub);

        $this->info("Repository {$name} created successfully.");
    }
}
