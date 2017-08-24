<?php

namespace Yaro\ApiDocs\Commands;

use Illuminate\Console\Command;

class BlueprintCreate extends Command
{
    protected $signature = 'apidocs:blueprint-create {name?} {--disc=}';

    protected $description = 'Create a new Blueprint API file.';

    public function handle()
    {
        $apiDocs = app()->make('yaro.apidocs');
        $snapshotName = $apiDocs->blueprint()->create((string) $this->argument('name'), (string) $this->option('disc'));

        $this->info(sprintf('New blueprint snapshot [%s] created successfully.', $snapshotName));
    }
}
