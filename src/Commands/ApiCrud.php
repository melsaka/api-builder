<?php

namespace Melsaka\ApiBuilder\Commands;

use Illuminate\Console\Command;
use Melsaka\ApiBuilder\Support\ModelInspector;
use Melsaka\ApiBuilder\Support\ValidationRuleBuilder;
use Melsaka\ApiBuilder\Support\StubGenerator;
use Illuminate\Support\Str;

class ApiCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:crud {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold CRUD API (Controller, Service, Repository, Resource, Requests, Policies, etc.)';

    public function __construct(
        protected ModelInspector $inspector,
        protected ValidationRuleBuilder $ruleBuilder,
        protected StubGenerator $stubGenerator
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $model = ucfirst($this->argument('model'));
        $modelClass = "App\\Models\\{$model}";

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelClass} does not exist.");
            return;
        }

        $table        = Str::snake(Str::pluralStudly($model));
        $modelVar     = lcfirst($model);
        $modelPlural  = Str::pluralStudly($model);

        $fields       = $this->inspector->getColumns($modelClass);
        $rules        = $this->ruleBuilder->build($modelClass);

        $resourceFields = $fields;

        $supportedImages = method_exists($modelClass, 'supportedImages') ? $modelClass::supportedImages() : [];

        $includes = count($supportedImages) ? "'images'" : '';

        $supportedImagesString = implode(',', array_keys($supportedImages));

        // Generate CRUD-specific files
        foreach ($this->stubGenerator->getCrudFiles($model, count($supportedImages)) as $file => $stub) {
            $this->stubGenerator->generateIfMissing($file, $stub, [
                '{{model}}'           => $model,
                '{{modelVariable}}'   => $modelVar,
                '{{modelPlural}}'     => $modelPlural,
                '{{table}}'           => $table,
                '{{includes}}'        => $includes,
                '{{fields}}'          => $this->stubGenerator->formatFields($fields),
                '{{validationRules}}' => $this->stubGenerator->formatRules($rules),
                '{{resource}}'        => $this->stubGenerator->formatResource($fields, $supportedImages),
                '{{supportedImagesString}}' => $supportedImagesString,
            ]);
        }

        $this->info("✅ CRUD files for {$model} created successfully!");
    }
}
