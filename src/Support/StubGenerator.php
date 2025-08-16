<?php

namespace Melsaka\ApiBuilder\Support;

class StubGenerator
{
    private const BASE_PATH = 'Commands/stubs';

    private function getStubPath(string $stubFile): string
    {
        // Check if stubs are published to app directory first
        $appStubPath = app_path('Console/Commands/stubs/' . $stubFile);
        if (file_exists($appStubPath)) {
            return $appStubPath;
        }

        // Fall back to package stubs
        return __DIR__ . '/../stubs/' . $stubFile;
    }

    public function getRequiredFiles(): array
    {
        return [
            "app/Http/Controllers/Api/ApiController.php"        => 'api-controller.stub',
            "app/Traits/ApiErrorResponses.php"                  => 'Traits/ApiErrorResponses.stub',
            "app/Traits/ApiOkResponses.php"                     => 'Traits/ApiOkResponses.stub',
            "app/Exceptions/AuthenticationException.php"        => 'Exceptions/AuthenticationException.stub',
            "app/Exceptions/AuthorizationException.php"         => 'Exceptions/AuthorizationException.stub',
            "app/Exceptions/BadRequestException.php"            => 'Exceptions/BadRequestException.stub',
            "app/Exceptions/BaseHttpException.php"              => 'Exceptions/BaseHttpException.stub',
            "app/Exceptions/ConflictException.php"              => 'Exceptions/ConflictException.stub',
            "app/Exceptions/NotFoundException.php"              => 'Exceptions/NotFoundException.stub',
            "app/Exceptions/PaymentGatewayException.php"        => 'Exceptions/PaymentGatewayException.stub',
            "app/Exceptions/ServerErrorException.php"           => 'Exceptions/ServerErrorException.stub',
            "app/Exceptions/ThrottleRequestsException.php"      => 'Exceptions/ThrottleRequestsException.stub',
            "app/Exceptions/TooManyRequestsException.php"       => 'Exceptions/TooManyRequestsException.stub',
            "app/Exceptions/UnprocessableEntityException.php"   => 'Exceptions/UnprocessableEntityException.stub',
        ];
    }

    public function getCrudFiles(string $model): array
    {
        return [
            "app/Routes/{$model}.php"                                   => 'route.stub',
            "app/Http/Controllers/Api/{$model}/{$model}Controller.php"  => 'controller.stub',
            "app/Services/{$model}Service.php"                          => 'service.stub',
            "app/Repositories/{$model}Repository.php"                   => 'repository.stub',
            "app/Http/Resources/{$model}Resource.php"                   => 'resource.stub',
            "app/Http/Requests/{$model}/Store{$model}Request.php"       => 'Requests/StoreRequest.stub',
            "app/Http/Requests/{$model}/Update{$model}Request.php"      => 'Requests/UpdateRequest.stub',
            "app/Policies/{$model}Policy.php"                           => 'policy.stub',
        ];
    }

    public function generateIfMissing(string $path, string $stubFile, array $replacements, bool $printSkipped = true): void
    {
        if (file_exists(base_path($path))) {
            echo $printSkipped ? "⚠️  Skipped: {$path} already exists\n" : '';
            return;
        }

        $stubPath = $this->getStubPath($stubFile);
        
        if (!file_exists($stubPath)) {
            echo "❌ Error: Stub file not found at {$stubPath}\n";
            return;
        }

        $stub = file_get_contents($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);
        
        $dir = dirname(base_path($path));
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(base_path($path), $content);
        echo "✅ Created: {$path}\n";
    }

    public function formatRules(array $rules): string
    {
        return collect($rules)
            ->map(fn($rule, $field) => "'{$field}' => '{$rule}'")
            ->implode(",\n            ");
    }

    public function formatFields(array $fields, bool $asResource = false): string
    {
        return collect($fields)
            ->map(fn($field) => $asResource ? "'{$field}' => \$this->{$field}" : "'{$field}'")
            ->implode(",\n            ");
    }
}
