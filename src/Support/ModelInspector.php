<?php

namespace Melsaka\ApiBuilder\Support;

use Illuminate\Support\Facades\Schema;

class ModelInspector
{
    public function getColumns(string $modelClass): array
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass;
        $table = $model->getTable();

        $columns = Schema::getColumnListing($table);

        return array_values(array_diff($columns, ['id','created_at','updated_at','deleted_at']));
    }
}
