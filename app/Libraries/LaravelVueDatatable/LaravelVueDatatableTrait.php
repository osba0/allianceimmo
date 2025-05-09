<?php

namespace App\Libraries\LaravelVueDatatable;

trait LaravelVueDatatableTrait
{
    public function scopeEloquentQuery($query, $orderBy, $orderByDir = 'asc', $searchValue = '', $relationships = [])
    {
        $queryBuilder = new QueryBuilder($this, $query, $this->dataTableColumns, $this->dataTableRelationships);

        $query = $queryBuilder->selectData()
            ->addRelationships($relationships, $orderByDir);

        if (!empty($orderBy)) {
            $query = $query->orderBy($orderBy, $orderByDir);
        }

        return $query->filter($searchValue)->getQuery();
    }
}
