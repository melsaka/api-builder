<?php

namespace Melsaka\ApiBuilder\Commands;

use Illuminate\Console\Command;
use Melsaka\ApiBuilder\Support\StubGenerator;

class ApiScaffold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the required base API files (controllers, traits, exceptions)';

    public function __construct(
        protected StubGenerator $stubGenerator
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Ensure required common files exist
        foreach ($this->stubGenerator->getRequiredFiles() as $file => $stub) {
            $this->stubGenerator->generateIfMissing($file, $stub, [], false);
        }

        $this->info("✅ Required files created successfully!");
    }
}
