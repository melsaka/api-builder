<?php

namespace Melsaka\ApiBuilder\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ValidationRuleBuilder
{
    public function build(string $modelClass): array
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass;

        $table = $model->getTable();
        $connection = $model->getConnectionName() ?? config('database.default');
        $columns = Schema::connection($connection)->getColumnListing($table);

        $rules = [];

        foreach ($columns as $column) {
            if (in_array($column, ['id','created_at','updated_at','deleted_at'])) {
                continue;
            }

            $type     = DB::connection($connection)->getSchemaBuilder()->getColumnType($table, $column);
            $nullable = $this->isNullable($connection, $table, $column);

            $ruleParts   = [$nullable ? 'nullable' : 'required'];
            $ruleParts[] = $this->mapTypeToRule($type);

            if (Str::endsWith($column, '_id')) {
                $relatedTable = Str::plural(Str::beforeLast($column, '_id'));
                $ruleParts[]  = "exists:{$relatedTable},id";
            }

            if (in_array($column, ['email','slug','username'])) {
                $ruleParts[] = "unique:{$table},{$column}";
            }

            $rules[$column] = implode('|', array_filter($ruleParts));
        }

        return $rules;
    }

    private function mapTypeToRule(string $type): ?string
    {
        return match ($type) {
            'string', 'text'       => 'string',
            'integer','bigint',
            'smallint'             => 'integer',
            'boolean'              => 'boolean',
            'date','datetime',
            'timestamp'            => 'date',
            'decimal','float',
            'double'               => 'numeric',
            default                => null,
        };
    }

    private function isNullable(string $connection, string $table, string $column): bool
    {
        if (!class_exists(\Doctrine\DBAL\DriverManager::class)) {
            return false; // fallback
        }

        $schemaManager = DB::connection($connection)->getDoctrineSchemaManager();
        $doctrineColumn = $schemaManager->listTableDetails($table)->getColumn($column);

        return !$doctrineColumn->getNotnull();
    }
}
